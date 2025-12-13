<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $q = request('q');
        $sort = request('sort', 'full_name');
        $sortDir = request('dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['full_name', 'employee_code', 'branch_name', 'is_volunteer'];

        $query = Employee::with('branch');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('employee_code', 'like', "%{$q}%");
            });
        }

        if ($sort === 'branch_name') {
            $query->leftJoin('branches', 'branches.id', '=', 'employees.branch_id')
                  ->orderBy('branches.name', $sortDir)
                  ->select('employees.*', 'branches.name as branch_name');
        } elseif (in_array($sort, $allowedSort)) {
            $query->leftJoin('branches', 'branches.id', '=', 'employees.branch_id')
                  ->select('employees.*', 'branches.name as branch_name')
                  ->orderBy($sort, $sortDir);
        } else {
            $query->leftJoin('branches', 'branches.id', '=', 'employees.branch_id')
                  ->select('employees.*', 'branches.name as branch_name')
                  ->orderBy('full_name', $sortDir);
        }

        $employees = $query->paginate(20)->withQueryString();

        // Scope companies list
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('employees.index', compact('employees', 'q', 'sort', 'sortDir', 'companies'));
    }

    public function customLocations()
    {
        $q = request('q');
        $sort = request('sort', 'full_name');
        $sortDir = request('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        // Filter employees who have at least one custom work location
        $query = Employee::whereHas('workLocations')->with('branch');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('employee_code', 'like', "%{$q}%");
            });
        }

        $query->orderBy('full_name', $sortDir);

        $employees = $query->paginate(20)->withQueryString();

        // reuse index view but we might want a different title or indicate filter is active
        // For simplicity, reuse index. 
        
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('employees.index', compact('employees', 'q', 'sort', 'sortDir', 'companies'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        $branches = collect(); 
        $departments = collect();
        $positions = collect();
        
        // If user has company_id, pre-load their branches/depts/positions?
        // For now, keep it empty as per original, assuming dynamic loading or user selects company first.
        // But if there is only 1 company, we might want to load them.
        if (Auth::user()->company_id) {
            $branches = Branch::where('company_id', Auth::user()->company_id)->pluck('name', 'id');
            $departments = Department::where('company_id', Auth::user()->company_id)->pluck('name', 'id');
            $positions = Position::where('company_id', Auth::user()->company_id)->pluck('name', 'id');
        }

        return view('employees.create', compact('companies', 'branches', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'full_name' => 'required|string|max:191',
            'nickname' => 'nullable|string|max:100',
            'national_id_number' => 'nullable|string|max:32',
            'family_card_number' => 'nullable|string|max:32',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'branch_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'position_id' => 'nullable|integer',
            'is_volunteer' => 'sometimes|boolean',
            'hourly_rate' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric',
            'employment_type' => 'required|in:permanent,contract,intern,outsourcing',
            'status' => 'required|in:active,inactive,suspended,terminated',
            'join_date' => 'nullable|date',
        ]);

        // Enforce company scope for non-super-admins
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
             abort(403, 'You cannot create employees for another company.');
        }

        $data['is_volunteer'] = $request->boolean('is_volunteer');
        $data['basic_salary'] = $data['basic_salary'] ?? 0;
        $data['hourly_rate'] = $data['hourly_rate'] ?? 0;
        $data['commission_rate'] = $data['commission_rate'] ?? 0;
        
        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created');
    }

    public function show($id)
    {
        $employee = Employee::with(['branch', 'department', 'position', 'company'])->findOrFail($id);
        
        // Check company scope
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $careerHistories = \App\Models\CareerHistory::where('employee_id', $id)
            ->with(['oldBranch', 'newBranch', 'oldPosition', 'newPosition', 'oldDepartment', 'newDepartment', 'creator'])
            ->orderByDesc('effective_date')
            ->get();

        $employee->load(['payrollComponents', 'educations', 'certifications']);

        return view('employees.show', compact('employee', 'careerHistories'));
    }

    public function edit($id)
    {
        // Employee::find($id) will automatically apply CompanyScope
        $employee = Employee::findOrFail($id);

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        $branches = Branch::where('company_id', $employee->company_id)->pluck('name', 'id');
        $departments = Department::where('company_id', $employee->company_id)->pluck('name', 'id');
        $positions = Position::where('company_id', $employee->company_id)->pluck('name', 'id');

        return view('employees.edit', compact('employee', 'companies', 'branches', 'departments', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $id,
            'full_name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'branch_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'position_id' => 'nullable|integer',
            'is_volunteer' => 'sometimes|boolean',
            'hourly_rate' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric',
            'employment_type' => 'required|in:permanent,contract,intern,outsourcing',
            'status' => 'required|in:active,inactive,suspended,terminated',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
             abort(403, 'You cannot move employees to another company.');
        }

        $data['is_volunteer'] = $request->boolean('is_volunteer');
        $data['basic_salary'] = $data['basic_salary'] ?? 0;
        $data['hourly_rate'] = $data['hourly_rate'] ?? 0;
        $data['commission_rate'] = $data['commission_rate'] ?? 0;
        
        $employee->update($data);

        // Sync Work Locations
        $employee->workLocations()->sync($request->input('work_locations', []));

        return redirect()->route('employees.index')->with('success', 'Employee updated');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\EmployeeExport, 'employees.xlsx');
    }

    public function importTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\EmployeeTemplateExport, 'employee_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\EmployeeImport, $request->file('file'));
            return redirect()->route('employees.index')->with('success', 'Employees imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('employees.index')->with('error', 'Import Validation Errors: ' . implode(' | ', $messages));
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Error importing employees: ' . $e->getMessage());
        }
    }

    public function storeComponent(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $request->validate([
            'payroll_component_id' => 'required|exists:payroll_components,id',
            'amount' => 'required|numeric',
        ]);

        $comp = \App\Models\PayrollComponent::find($request->payroll_component_id);
        
        // Attach or Update
        $employee->payrollComponents()->syncWithoutDetaching([
            $comp->id => [
                'amount' => $request->amount,
                'effective_from' => now()->toDateString(), // Simplified
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        return redirect()->back()->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function destroyComponent($id, $componentId)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $employee->payrollComponents()->detach($componentId);

        return redirect()->back()->with('success', 'Komponen gaji berhasil dihapus.');
    }

    public function storeEducation(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $data = $request->validate([
            'institution_name' => 'required|string|max:191',
            'degree' => 'required|string|max:50',
            'major' => 'nullable|string|max:100',
            'start_year' => 'nullable|integer|digits:4',
            'end_year' => 'nullable|integer|digits:4',
            'gpa' => 'nullable|numeric|between:0,4.00',
            'notes' => 'nullable|string',
        ]);

        $employee->educations()->create($data);

        return redirect()->back()->with('success', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function destroyEducation($id, $educationId)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $employee->educations()->where('id', $educationId)->delete();

        return redirect()->back()->with('success', 'Riwayat pendidikan berhasil dihapus.');
    }

    public function storeCertification(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'issuer' => 'required|string|max:191',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'credential_number' => 'nullable|string|max:100',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $employee->certifications()->create($data);

        return redirect()->back()->with('success', 'Sertifikasi berhasil ditambahkan.');
    }

    public function destroyCertification($id, $certificationId)
    {
        $employee = Employee::findOrFail($id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) abort(403);

        $employee->certifications()->where('id', $certificationId)->delete();

        return redirect()->back()->with('success', 'Sertifikasi berhasil dihapus.');
    }
}
