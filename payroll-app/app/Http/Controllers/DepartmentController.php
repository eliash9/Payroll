<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $query = Department::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $departments = $query->orderBy('name')->paginate(20);

        return view('masters.departments.index', compact('departments'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.departments.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:departments,code',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        Department::create($data);

        return redirect()->route('departments.index')->with('success', 'Departemen ditambahkan.');
    }

    public function edit(int $id)
    {
        $department = Department::findOrFail($id);
        
        if (Auth::user()->company_id && $department->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this department.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.departments.edit', compact('department', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $department = Department::findOrFail($id);
        
        if (Auth::user()->company_id && $department->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this department.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $id,
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $department->update($data);

        return redirect()->route('departments.index')->with('success', 'Departemen diperbarui.');
    }

    public function destroy(int $id)
    {
        $department = Department::findOrFail($id);
        
        if (Auth::user()->company_id && $department->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this department.');
        }

        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen dihapus.');
    }
}
