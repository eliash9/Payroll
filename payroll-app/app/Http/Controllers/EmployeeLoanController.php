<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanSchedule;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeLoanController extends Controller
{
    public function index()
    {
        $unpaidInstallmentsQuery = DB::table('employee_loan_schedules')
            ->select('employee_loan_id', DB::raw('count(*) as count'))
            ->where('is_paid', false)
            ->groupBy('employee_loan_id');

        $query = DB::table('employee_loans as el')
            ->join('employees as e', 'e.id', '=', 'el.employee_id')
            ->join('companies as c', 'c.id', '=', 'el.company_id')
            ->leftJoinSub($unpaidInstallmentsQuery, 'els', function ($join) {
                $join->on('el.id', '=', 'els.employee_loan_id');
            })
            ->select(
                'el.*',
                'e.full_name',
                'e.employee_code',
                'c.name as company_name',
                DB::raw('COALESCE(els.count, 0) as unpaid_installments')
            );

        if (Auth::user()->company_id) {
            $query->where('el.company_id', Auth::user()->company_id);
        }

        $loans = $query->orderByDesc('el.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('transactions.employee_loans.index', compact('loans'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->orderBy('name')->pluck('name', 'id');
        
        $companyId = old('company_id') ?? request()->integer('company_id');
        if (Auth::user()->company_id) {
            $companyId = Auth::user()->company_id;
        }

        $employees = collect();
        $periods = collect();

        if ($companyId) {
             $employees = Employee::where('company_id', $companyId)
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'employee_code', 'company_id']);
                
             $periods = PayrollPeriod::where('company_id', $companyId)
                ->orderByDesc('start_date')
                ->get(['id', 'code', 'name', 'company_id']);
        }

        return view('transactions.employee_loans.create', compact('companies', 'employees', 'periods', 'companyId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'loan_number' => ['required', 'string', 'max:50', Rule::unique('employee_loans', 'loan_number')],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'installment_amount' => ['required', 'numeric', 'min:0'],
            'tenor_months' => ['required', 'integer', 'min:1'],
            'start_period_id' => ['required', 'integer', 'exists:payroll_periods,id'],
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        // Validasi employee-company konsisten
        $employee = Employee::findOrFail($data['employee_id']);
        if ($employee->company_id !== (int) $data['company_id']) {
            return back()->with('error', 'Employee tidak sesuai company')->withInput();
        }

        $startPeriod = PayrollPeriod::where('id', $data['start_period_id'])->where('company_id', $data['company_id'])->first();
        if (!$startPeriod) {
            return back()->with('error', 'Periode mulai tidak ditemukan untuk company tersebut')->withInput();
        }

        $periods = PayrollPeriod::where('company_id', $data['company_id'])
            ->where('start_date', '>=', $startPeriod->start_date)
            ->orderBy('start_date')
            ->limit($data['tenor_months'])
            ->get();

        if ($periods->count() < $data['tenor_months']) {
            return back()->with('error', 'Periode payroll belum lengkap untuk tenor yang diminta')->withInput();
        }

        DB::transaction(function () use ($data, $periods) {
            $now = now();
            // Assuming EmployeeLoan model exists or using DB if simpler for transaction
            // Let's use DB for consistency with original logic but scoped
            
            $loanId = DB::table('employee_loans')->insertGetId([
                'employee_id' => $data['employee_id'],
                'company_id' => $data['company_id'],
                'loan_number' => $data['loan_number'],
                'principal_amount' => $data['principal_amount'],
                'remaining_amount' => $data['principal_amount'],
                'installment_amount' => $data['installment_amount'],
                'start_period_id' => $data['start_period_id'],
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $schedules = [];
            $remaining = $data['principal_amount'];
            foreach ($periods as $idx => $period) {
                $amount = min($data['installment_amount'], $remaining);
                $remaining -= $amount;
                $schedules[] = [
                    'employee_loan_id' => $loanId,
                    'payroll_period_id' => $period->id,
                    'amount' => $amount,
                    'is_paid' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('employee_loan_schedules')->insert($schedules);

            DB::table('employee_loans')->where('id', $loanId)->update(['remaining_amount' => $remaining, 'updated_at' => now()]);
        });

        return redirect()->route('employee-loans.index')->with('success', 'Pinjaman karyawan ditambahkan beserta jadwal cicilan.');
    }

    public function edit(int $id)
    {
        $loan = DB::table('employee_loans')->find($id);
        abort_unless($loan, 404);
        
        if (Auth::user()->company_id && $loan->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->orderBy('name')->pluck('name', 'id');

        $employees = Employee::where('company_id', $loan->company_id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'employee_code', 'company_id']);
            
        $periods = PayrollPeriod::where('company_id', $loan->company_id)
            ->orderByDesc('start_date')
            ->get(['id', 'code', 'name', 'company_id']);

        return view('transactions.employee_loans.edit', compact('loan', 'companies', 'employees', 'periods'));
    }

    public function update(Request $request, int $id)
    {
        $loan = DB::table('employee_loans')->find($id);
        abort_unless($loan, 404);
        
        if (Auth::user()->company_id && $loan->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $data = $request->validate([
            'loan_number' => ['required', 'string', 'max:50', Rule::unique('employee_loans', 'loan_number')->ignore($id)],
            'status' => ['required', Rule::in(['active', 'completed', 'cancelled'])],
            'installment_amount' => ['required', 'numeric', 'min:0'],
            'remaining_amount' => ['required', 'numeric', 'min:0'],
        ]);

        DB::table('employee_loans')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('employee-loans.index')->with('success', 'Pinjaman diperbarui.');
    }

    public function destroy(int $id)
    {
        $loan = DB::table('employee_loans')->find($id);
        if ($loan && Auth::user()->company_id && $loan->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        DB::table('employee_loan_schedules')->where('employee_loan_id', $id)->delete();
        DB::table('employee_loans')->where('id', $id)->delete();

        return back()->with('success', 'Pinjaman dihapus.');
    }
}
