<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeBpjsController extends Controller
{
    public function index()
    {
        $query = DB::table('employee_bpjs')
            ->join('employees', 'employees.id', '=', 'employee_bpjs.employee_id')
            ->select('employee_bpjs.*', 'employees.full_name', 'employees.employee_code');

        if (Auth::user()->company_id) {
            $query->where('employees.company_id', Auth::user()->company_id);
        }

        $bpjs = $query->orderBy('employees.full_name')
            ->paginate(20);

        return view('transactions.employee_bpjs.index', compact('bpjs'));
    }

    public function create()
    {
        $employeesQuery = Employee::query();
        if (Auth::user()->company_id) {
            $employeesQuery->where('company_id', Auth::user()->company_id);
        }
        $employees = $employeesQuery->orderBy('full_name')->pluck('full_name', 'id');
        
        return view('transactions.employee_bpjs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'bpjs_kesehatan_number' => 'nullable|string|max:50',
            'bpjs_kesehatan_class' => 'nullable|string|max:10',
            'bpjs_ketenagakerjaan_number' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Verify employee belongs to company
        $employee = Employee::findOrFail($data['employee_id']);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Invalid employee selected.');
        }

        $now = now();
        DB::table('employee_bpjs')->updateOrInsert(
            ['employee_id' => $data['employee_id']],
            array_merge([
                'bpjs_kesehatan_number' => null,
                'bpjs_kesehatan_class' => null,
                'bpjs_ketenagakerjaan_number' => null,
                'start_date' => null,
                'is_active' => true,
            ], $data, [
                'updated_at' => $now,
                'created_at' => $now,
            ])
        );

        return redirect()->route('employee-bpjs.index')->with('success', 'Data BPJS karyawan tersimpan.');
    }

    public function edit(int $id)
    {
        $bpjs = DB::table('employee_bpjs')->find($id);
        abort_unless($bpjs, 404);

        // Verify access
        $employee = Employee::find($bpjs->employee_id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this data.');
        }

        $employeesQuery = Employee::query();
        if (Auth::user()->company_id) {
            $employeesQuery->where('company_id', Auth::user()->company_id);
        }
        $employees = $employeesQuery->orderBy('full_name')->pluck('full_name', 'id');

        return view('transactions.employee_bpjs.edit', compact('bpjs', 'employees'));
    }

    public function update(Request $request, int $id)
    {
        $bpjs = DB::table('employee_bpjs')->find($id);
        abort_unless($bpjs, 404);

        // Verify access
        $employee = Employee::find($bpjs->employee_id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this data.');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'bpjs_kesehatan_number' => 'nullable|string|max:50',
            'bpjs_kesehatan_class' => 'nullable|string|max:10',
            'bpjs_ketenagakerjaan_number' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Verify if employee changed (though usually hidden/disabled in edit, but for safety)
        if ($data['employee_id'] != $bpjs->employee_id) {
            $newEmployee = Employee::find($data['employee_id']);
            if (Auth::user()->company_id && $newEmployee->company_id != Auth::user()->company_id) {
                abort(403, 'Invalid employee selected.');
            }
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['updated_at'] = now();

        DB::table('employee_bpjs')->where('id', $id)->update($data);

        return redirect()->route('employee-bpjs.index')->with('success', 'Data BPJS karyawan diperbarui.');
    }

    public function destroy(int $id)
    {
        $bpjs = DB::table('employee_bpjs')->find($id);
        if ($bpjs) {
            $employee = Employee::find($bpjs->employee_id);
            if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
                abort(403, 'Unauthorized access to this data.');
            }
            DB::table('employee_bpjs')->where('id', $id)->delete();
        }
        
        return redirect()->route('employee-bpjs.index')->with('success', 'Data BPJS karyawan dihapus.');
    }
}
