<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $q = request('q');
        $sort = request('sort', 'full_name');
        $sortDir = request('dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['full_name', 'employee_code', 'branch_name', 'is_volunteer'];

        $employees = DB::table('employees')
            ->leftJoin('branches', 'branches.id', '=', 'employees.branch_id')
            ->select('employees.*', 'branches.name as branch_name')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('employees.full_name', 'like', "%{$q}%")
                        ->orWhere('employees.employee_code', 'like', "%{$q}%");
                });
            })
            ->orderBy(in_array($sort, $allowedSort) ? $sort : 'full_name', $sortDir)
            ->paginate(20)
            ->withQueryString();

        $companies = DB::table('companies')->pluck('name', 'id');

        return view('employees.index', compact('employees', 'q', 'sort', 'sortDir', 'companies'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        $branches = collect(); // loaded via ajax or dependent selection
        $departments = collect();
        $positions = collect();
        return view('employees.create', compact('companies', 'branches', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
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

        $data = array_merge([
            'created_at' => now(),
            'updated_at' => now(),
        ], $data);

        $data['is_volunteer'] = $request->boolean('is_volunteer');

        DB::table('employees')->insert($data);

        return redirect()->route('employees.index')->with('success', 'Employee created');
    }

    public function edit(int $id)
    {
        $employee = DB::table('employees')->find($id);
        $companies = DB::table('companies')->pluck('name', 'id');
        $branches = DB::table('branches')->where('company_id', $employee->company_id)->pluck('name', 'id');
        $departments = DB::table('departments')->where('company_id', $employee->company_id)->pluck('name', 'id');
        $positions = DB::table('positions')->where('company_id', $employee->company_id)->pluck('name', 'id');
        return view('employees.edit', compact('employee', 'companies', 'branches', 'departments', 'positions'));
    }

    public function update(Request $request, int $id)
    {
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

        $data['is_volunteer'] = $request->boolean('is_volunteer');
        $data['updated_at'] = now();
        DB::table('employees')->where('id', $id)->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated');
    }

    public function destroy(int $id)
    {
        DB::table('employees')->where('id', $id)->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted');
    }
}
