<?php

namespace App\Http\Controllers;

use App\Models\PayrollComponent;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayrollComponentController extends Controller
{
    public function index()
    {
        $query = PayrollComponent::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $components = $query->orderBy('sequence')
            ->orderBy('name')
            ->paginate(20);

        return view('masters.payroll_components.index', compact('components'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
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

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        PayrollComponent::create(array_merge([
            'is_taxable' => true,
            'show_in_payslip' => true,
            'sequence' => 0,
        ], $data));

        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll ditambahkan.');
    }

    public function edit(int $id)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);
        
        if (Auth::user()->company_id && $payrollComponent->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this component.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.payroll_components.edit', compact('payrollComponent', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);
        
        if (Auth::user()->company_id && $payrollComponent->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this component.');
        }

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
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $data['is_taxable'] = $request->boolean('is_taxable');
        $data['show_in_payslip'] = $request->boolean('show_in_payslip');
        $data['sequence'] = $data['sequence'] ?? 0;

        $payrollComponent->update($data);

        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll diperbarui.');
    }

    public function destroy(int $id)
    {
        $payrollComponent = PayrollComponent::findOrFail($id);
        
        if (Auth::user()->company_id && $payrollComponent->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this component.');
        }

        $payrollComponent->delete();
        return redirect()->route('payroll-components.index')->with('success', 'Komponen payroll dihapus.');
    }

    public function bulkAssign()
    {
        $components = PayrollComponent::where('company_id', Auth::user()->company_id ?? 1)->orderBy('name')->get();
        $branches = \App\Models\Branch::where('company_id', Auth::user()->company_id ?? 1)->pluck('name', 'id');
        $departments = \App\Models\Department::where('company_id', Auth::user()->company_id ?? 1)->pluck('name', 'id');
        $positions = \App\Models\Position::where('company_id', Auth::user()->company_id ?? 1)->pluck('name', 'id');

        return view('masters.payroll_components.bulk_assign', compact('components', 'branches', 'departments', 'positions'));
    }

    public function storeBulkAssign(Request $request)
    {
        $request->validate([
            'payroll_component_id' => 'required|exists:payroll_components,id',
            'amount' => 'required|numeric',
            'target_type' => 'required|in:all,branch,department,position,volunteer,regular',
            'target_value' => 'nullable', 
        ]);

        $query = \App\Models\Employee::where('company_id', Auth::user()->company_id ?? 1)
            ->where('status', 'active');

        switch ($request->target_type) {
            case 'branch':
                $query->where('branch_id', $request->target_value);
                break;
            case 'department':
                $query->where('department_id', $request->target_value);
                break;
            case 'position':
                $query->where('position_id', $request->target_value);
                break;
            case 'volunteer':
                $query->where('is_volunteer', true);
                break;
            case 'regular':
                $query->where('is_volunteer', false);
                break;
        }

        $employees = $query->get();
        $count = 0;

        foreach ($employees as $employee) {
            $employee->payrollComponents()->syncWithoutDetaching([
                $request->payroll_component_id => [
                    'amount' => $request->amount,
                    'effective_from' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
            $count++;
        }

        return redirect()->route('payroll-components.index')
            ->with('success', "Komponen berhasil diterapkan ke {$count} karyawan.");
    }
}
