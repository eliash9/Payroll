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

    public function edit(int $periodId, int $employeeId)
    {
        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$header) {
            abort(404, 'Slip not found');
        }

        // Verify access
        $employee = Employee::findOrFail($employeeId);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
             abort(403, 'Unauthorized access to this payslip.');
        }

        $period = DB::table('payroll_periods')->find($periodId);
        if (in_array($period->status, ['approved', 'closed'])) {
            return redirect()->route('payslips.show', [$periodId, $employeeId])->with('error', 'Periode sudah dikunci, tidak bisa diedit.');
        }
        
        $details = DB::table('payroll_details as pd')
            ->join('payroll_components as pc', 'pc.id', '=', 'pd.payroll_component_id')
            ->select('pd.id', 'pc.name', 'pc.type', 'pc.code', 'pd.amount', 'pd.quantity', 'pd.remark')
            ->where('payroll_header_id', $header->id)
            ->orderBy('pc.sequence')
            ->get();

        $availableComponents = DB::table('payroll_components')
            ->where('company_id', $period->company_id)
            ->where('calculation_method', 'manual') // Only manual components usually added manually
            ->orderBy('name')
            ->get();

        return view('payslip_edit', [
            'header' => $header,
            'period' => $period,
            'employee' => $employee,
            'details' => $details,
            'availableComponents' => $availableComponents,
        ]);
    }

    public function update(Request $request, int $periodId, int $employeeId)
    {
        $period = DB::table('payroll_periods')->find($periodId);
        if (in_array($period->status, ['approved', 'closed'])) {
            abort(403, 'Period is locked.');
        }

        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();
            
        if (!$header) {
            abort(404);
        }

        if (Auth::user()->company_id && $period->company_id != Auth::user()->company_id) {
            abort(403);
        }

        DB::transaction(function () use ($request, $header, $period) {
            // Update existing details
            if ($request->has('details')) {
                foreach ($request->details as $detailId => $data) {
                    $amount = (float) str_replace(',', '', $data['amount']);
                    // Keep sign logic consistent with DB storage (deductions negative)
                    // However, user input usually positive. We need to check the component type for existing details.
                    // But we don't have component type easily here unless we query or pass it.
                    // simpler: We assume user inputs absolute value, and we apply sign based on component type.
                    // But wait, existing details stored in DB are already signed.
                    // If user edits "-50000" to "-60000", it works. If they edit "50000" (displayed as positive in view?)
                    
                    // Let's assume the View handles display as Positive for both, so we need to know the type to save as Negative.
                    // Query the component type for this detail
                    $detailComp = DB::table('payroll_details')
                        ->join('payroll_components', 'payroll_components.id', '=', 'payroll_details.payroll_component_id')
                        ->where('payroll_details.id', $detailId)
                        ->select('payroll_components.type')
                        ->first();
                        
                    if ($detailComp) {
                         if ($detailComp->type === 'deduction') {
                             $amount = -1 * abs($amount);
                         } else {
                             $amount = abs($amount);
                         }
                         
                        DB::table('payroll_details')
                            ->where('id', $detailId)
                            ->where('payroll_header_id', $header->id)
                            ->update([
                                'amount' => $amount,
                                'quantity' => $data['quantity'] ?? null,
                                'remark' => $data['remark'] ?? null,
                                'updated_at' => now(),
                            ]);
                    }
                }
            }

            // Remove details
            if ($request->has('remove_details')) {
                DB::table('payroll_details')
                    ->whereIn('id', $request->remove_details)
                    ->where('payroll_header_id', $header->id)
                    ->delete();
            }

            // Add new component
            if ($request->filled('new_component_id') && $request->filled('new_amount')) {
                $comp = DB::table('payroll_components')->find($request->new_component_id);
                if ($comp) {
                    $amount = (float) str_replace(',', '', $request->new_amount);
                    if ($comp->type === 'deduction') {
                        $amount = -1 * abs($amount);
                    } else {
                        $amount = abs($amount);
                    }

                    DB::table('payroll_details')->insert([
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $comp->id,
                        'amount' => $amount,
                        'quantity' => $request->new_quantity ?? 1,
                        'remark' => $request->new_remark ?? 'Manual entry',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Recalculate Header Totals
            $details = DB::table('payroll_details')
                ->join('payroll_components', 'payroll_components.id', '=', 'payroll_details.payroll_component_id')
                ->where('payroll_header_id', $header->id)
                ->select('payroll_details.amount') // Amount is signed
                ->get();

            $gross = 0;
            $deduction = 0;

            foreach ($details as $d) {
                if ($d->amount > 0) {
                    $gross += $d->amount;
                } else {
                    $deduction += abs($d->amount);
                }
            }
            $net = $gross - $deduction;

            DB::table('payroll_headers')->where('id', $header->id)->update([
                'gross_income' => $gross,
                'total_deduction' => $deduction,
                'net_income' => $net,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('payroll.periods.show', $periodId)->with('success', 'Slip gaji berhasil diperbarui.');
    }
}
