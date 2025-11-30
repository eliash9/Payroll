<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\CareerHistory;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutationController extends Controller
{
    public function create(Employee $employee)
    {
        // Check scope
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $companyId = $employee->company_id;
        $branches = Branch::where('company_id', $companyId)->pluck('name', 'id');
        $departments = Department::where('company_id', $companyId)->pluck('name', 'id');
        $positions = Position::where('company_id', $companyId)->pluck('name', 'id');

        return view('employees.mutations.create', compact('employee', 'branches', 'departments', 'positions'));
    }

    public function store(Request $request, Employee $employee)
    {
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'effective_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'new_branch_id' => 'nullable|exists:branches,id',
            'new_department_id' => 'nullable|exists:departments,id',
            'new_position_id' => 'nullable|exists:positions,id',
            'new_employment_type' => 'nullable|in:permanent,contract,intern,outsourcing',
            'new_status' => 'nullable|in:active,inactive,suspended,terminated',
            'new_basic_salary' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $employee, $validated) {
            // 1. Create History
            CareerHistory::create([
                'company_id' => $employee->company_id,
                'employee_id' => $employee->id,
                'type' => $validated['type'],
                'effective_date' => $validated['effective_date'],
                'reference_number' => $validated['reference_number'],
                
                // Snapshot Old State
                'old_branch_id' => $employee->branch_id,
                'old_department_id' => $employee->department_id,
                'old_position_id' => $employee->position_id,
                'old_employment_status' => $employee->employment_type, // Map to employment_type
                'old_basic_salary' => $employee->basic_salary,

                // New State
                'new_branch_id' => $validated['new_branch_id'] ?? $employee->branch_id,
                'new_department_id' => $validated['new_department_id'] ?? $employee->department_id,
                'new_position_id' => $validated['new_position_id'] ?? $employee->position_id,
                'new_employment_status' => $validated['new_employment_type'] ?? $employee->employment_type,
                'new_basic_salary' => $validated['new_basic_salary'] ?? $employee->basic_salary,
                
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // 2. Update Employee
            $employee->update([
                'branch_id' => $validated['new_branch_id'] ?? $employee->branch_id,
                'department_id' => $validated['new_department_id'] ?? $employee->department_id,
                'position_id' => $validated['new_position_id'] ?? $employee->position_id,
                'employment_type' => $validated['new_employment_type'] ?? $employee->employment_type,
                'status' => $validated['new_status'] ?? $employee->status, // Allow status change (e.g. termination)
                'basic_salary' => $validated['new_basic_salary'] ?? $employee->basic_salary,
            ]);
        });

        return redirect()->route('employees.show', $employee->id)->with('success', 'Mutasi berhasil disimpan');
    }
}
