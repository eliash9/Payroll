<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollComponentController extends Controller
{
    public function index()
    {
        $components = DB::table('payroll_components')
            ->join('companies', 'companies.id', '=', 'payroll_components.company_id')
            ->select('payroll_components.*', 'companies.name as company_name')
            ->orderBy('payroll_components.sequence')
            ->orderBy('payroll_components.name')
            ->paginate(20);

        return view('masters.payroll_components.index', compact('components'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.payroll_components.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:payroll_components,code',
            'type' => 'required|in:earning,deduction',
            'category' => 'required|in:fixed,variable,kpi,bpjs,tax,loan,other',
            'calculation_method' => 'required|in:manual,formula,attendance_based,kpi_based',
            'is_taxable' => 'boolean',
            'show_in_payslip' => 'boolean',
            'sequence' => 'nullable|integer',
            'formula' => 'nullable|string',
        ]);

        $now = now();
        DB::table('payroll_components')->insert(array_merge([
            'is_taxable' => true,
            'show_in_payslip' => true,
            'sequence' => 0,
        ], $data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll ditambahkan.');
    }

    public function edit(int $id)
    {
        $payrollComponent = DB::table('payroll_components')->find($id);
        abort_unless($payrollComponent, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.payroll_components.edit', compact('payrollComponent', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $payrollComponent = DB::table('payroll_components')->find($id);
        abort_unless($payrollComponent, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:payroll_components,code,' . $id,
            'type' => 'required|in:earning,deduction',
            'category' => 'required|in:fixed,variable,kpi,bpjs,tax,loan,other',
            'calculation_method' => 'required|in:manual,formula,attendance_based,kpi_based',
            'is_taxable' => 'boolean',
            'show_in_payslip' => 'boolean',
            'sequence' => 'nullable|integer',
            'formula' => 'nullable|string',
        ]);

        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['show_in_payslip'] = $request->boolean('show_in_payslip');
        $data['sequence'] = $data['sequence'] ?? 0;
        $data['updated_at'] = now();

        DB::table('payroll_components')->where('id', $id)->update($data);

        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('payroll_components')->where('id', $id)->delete();
        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll dihapus.');
    }
}
