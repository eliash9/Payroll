<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollHeader;
use App\Models\PayrollPeriod;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Illuminate\Pagination\LengthAwarePaginator;

class PayslipController extends Controller
{
    public function show(int $periodId, int $employeeId)
    {
        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$header) {
            throw new RuntimeException('Slip tidak ditemukan. Pastikan payroll sudah digenerate.');
        }

        // Verify access
        $employee = Employee::findOrFail($employeeId);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
             abort(403, 'Unauthorized access to this payslip.');
        }

        $period = DB::table('payroll_periods')->find($periodId);
        
        $details = DB::table('payroll_details as pd')
            ->join('payroll_components as pc', 'pc.id', '=', 'pd.payroll_component_id')
            ->select('pc.name', 'pc.type', 'pc.code', 'pd.amount', 'pd.quantity')
            ->where('payroll_header_id', $header->id)
            ->orderBy('pc.sequence')
            ->get();

        $data = [
            'header' => $header,
            'period' => $period,
            'employee' => $employee,
            'details' => $details,
        ];

        if (request()->query('format') === 'pdf') {
            $pdf = app('dompdf.wrapper')
                ->setPaper('A4', 'portrait')
                ->loadView('payslip_pdf', $data);
            $filename = 'Slip-' . $period->code . '-' . $employee->employee_code . '.pdf';
            return $pdf->download($filename);
        }

        return view('payslip', $data);
    }

    /**
     * List slip per periode (opsional filter employee_id).
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->orderBy('name')->pluck('name', 'id');
        
        $companyId = $request->input('company_id');
        if (Auth::user()->company_id) {
            $companyId = Auth::user()->company_id;
        } elseif (!$companyId) {
            $companyId = $companies->keys()->first();
        }

        $periodsList = DB::table('payroll_periods')
            ->where('company_id', $companyId)
            ->orderByDesc('start_date')
            ->get();

        $period = null;
        if ($request->filled('period')) {
            $period = DB::table('payroll_periods')
                ->where('company_id', $companyId)
                ->where('code', $request->input('period'))
                ->first();
        } else {
            $period = $periodsList->first();
        }

        if (!$period) {
            $empty = new LengthAwarePaginator([], 0, 20);
            return view('payslip_list', [
                'headers' => $empty,
                'period' => null,
                'companies' => $companies,
                'companyId' => $companyId,
                'periodsList' => $periodsList,
                'employees' => collect(),
            ]);
        }

        $query = DB::table('payroll_headers as ph')
            ->join('employees as e', 'e.id', '=', 'ph.employee_id')
            ->select('ph.id', 'ph.payroll_period_id', 'ph.employee_id', 'ph.net_income', 'ph.gross_income', 'e.full_name', 'e.employee_code', 'e.is_volunteer')
            ->where('ph.payroll_period_id', $period->id);
            
        // Ensure we only see employees from the selected company (which is already scoped for non-admins)
        $query->where('e.company_id', $companyId);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('e.full_name', 'like', "%{$q}%")
                    ->orWhere('e.employee_code', 'like', "%{$q}%");
            });
        }
        if ($request->filled('employee_id')) {
            $query->where('ph.employee_id', $request->input('employee_id'));
        }

        $headers = $query->orderBy('e.full_name')->paginate(20)->withQueryString();

        $employees = DB::table('employees')
            ->where('company_id', $companyId)
            ->orderBy('full_name')
            ->pluck('full_name', 'id');

        return view('payslip_list', [
            'headers' => $headers,
            'period' => $period,
            'companies' => $companies,
            'companyId' => $companyId,
            'periodsList' => $periodsList,
            'employees' => $employees,
        ]);
    }
}
