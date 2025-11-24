<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeLoanController extends Controller
{
    public function index()
    {
        $loans = DB::table('employee_loans as el')
            ->join('employees as e', 'e.id', '=', 'el.employee_id')
            ->join('companies as c', 'c.id', '=', 'el.company_id')
            ->leftJoin('employee_loan_schedules as els', function ($join) {
                $join->on('els.employee_loan_id', '=', 'el.id')->where('els.is_paid', false);
            })
            ->select(
                'el.*',
                'e.full_name',
                'e.employee_code',
                'c.name as company_name',
                DB::raw('COUNT(els.id) as unpaid_installments')
            )
            ->groupBy('el.id', 'e.full_name', 'e.employee_code', 'c.name')
            ->orderByDesc('el.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('transactions.employee_loans.index', compact('loans'));
    }

    public function create()
    {
        $companies = DB::table('companies')->orderBy('name')->pluck('name', 'id');
        $companyId = old('company_id') ?? request()->integer('company_id');
        $employees = DB::table('employees')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'employee_code', 'company_id']);
        $periods = DB::table('payroll_periods')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->orderByDesc('start_date')
            ->get(['id', 'code', 'name', 'company_id']);

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

        // Validasi employee-company konsisten
        $empCompany = DB::table('employees')->where('id', $data['employee_id'])->value('company_id');
        if ($empCompany !== (int) $data['company_id']) {
            return back()->with('error', 'Employee tidak sesuai company')->withInput();
        }

        $startPeriod = DB::table('payroll_periods')->where('id', $data['start_period_id'])->where('company_id', $data['company_id'])->first();
        if (!$startPeriod) {
            return back()->with('error', 'Periode mulai tidak ditemukan untuk company tersebut')->withInput();
        }

        $periods = DB::table('payroll_periods')
            ->where('company_id', $data['company_id'])
            ->where('start_date', '>=', $startPeriod->start_date)
            ->orderBy('start_date')
            ->limit($data['tenor_months'])
            ->get();

        if ($periods->count() < $data['tenor_months']) {
            return back()->with('error', 'Periode payroll belum lengkap untuk tenor yang diminta')->withInput();
        }

        DB::transaction(function () use ($data, $periods) {
            $now = now();
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

        $companies = DB::table('companies')->orderBy('name')->pluck('name', 'id');
        $employees = DB::table('employees')
            ->where('company_id', $loan->company_id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'employee_code', 'company_id']);
        $periods = DB::table('payroll_periods')
            ->where('company_id', $loan->company_id)
            ->orderByDesc('start_date')
            ->get(['id', 'code', 'name', 'company_id']);

        return view('transactions.employee_loans.edit', compact('loan', 'companies', 'employees', 'periods'));
    }

    public function update(Request $request, int $id)
    {
        $loan = DB::table('employee_loans')->find($id);
        abort_unless($loan, 404);

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
        DB::table('employee_loan_schedules')->where('employee_loan_id', $id)->delete();
        DB::table('employee_loans')->where('id', $id)->delete();

        return back()->with('success', 'Pinjaman dihapus.');
    }
}
