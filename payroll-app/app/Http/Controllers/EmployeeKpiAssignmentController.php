<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\KpiMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeKpiAssignmentController extends Controller
{
    public function index()
    {
        $query = DB::table('employee_kpi_assignments')
            ->join('employees', 'employees.id', '=', 'employee_kpi_assignments.employee_id')
            ->join('kpi_master', 'kpi_master.id', '=', 'employee_kpi_assignments.kpi_id')
            ->select(
                'employee_kpi_assignments.*',
                'employees.full_name as employee_name',
                'employees.employee_code',
                'kpi_master.name as kpi_name',
                'kpi_master.code as kpi_code'
            );

        if (Auth::user()->company_id) {
            $query->where('employees.company_id', Auth::user()->company_id);
        }

        $assignments = $query->orderBy('employees.full_name')
            ->orderBy('kpi_master.name')
            ->paginate(20);

        return view('masters.employee_kpi.index', compact('assignments'));
    }

    public function create()
    {
        $employeesQuery = Employee::query();
        $kpisQuery = KpiMaster::query();

        if (Auth::user()->company_id) {
            $employeesQuery->where('company_id', Auth::user()->company_id);
            $kpisQuery->where('company_id', Auth::user()->company_id);
        }

        $employees = $employeesQuery->orderBy('full_name')->pluck('full_name', 'id');
        $kpis = $kpisQuery->orderBy('name')->pluck('name', 'id');
        
        return view('masters.employee_kpi.create', compact('employees', 'kpis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'kpi_id' => 'required|integer|exists:kpi_master,id',
            'target' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Verify employee and KPI belong to company
        $employee = Employee::findOrFail($data['employee_id']);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Invalid employee selected.');
        }

        $kpi = KpiMaster::findOrFail($data['kpi_id']);
        if (Auth::user()->company_id && $kpi->company_id != Auth::user()->company_id) {
            abort(403, 'Invalid KPI selected.');
        }

        $now = now();
        DB::table('employee_kpi_assignments')->insert(array_merge($data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('employee-kpi.index')->with('success', 'KPI karyawan ditambahkan.');
    }

    public function edit(int $id)
    {
        $assignment = DB::table('employee_kpi_assignments')->find($id);
        abort_unless($assignment, 404);

        // Verify access
        $employee = Employee::find($assignment->employee_id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        $employeesQuery = Employee::query();
        $kpisQuery = KpiMaster::query();

        if (Auth::user()->company_id) {
            $employeesQuery->where('company_id', Auth::user()->company_id);
            $kpisQuery->where('company_id', Auth::user()->company_id);
        }

        $employees = $employeesQuery->orderBy('full_name')->pluck('full_name', 'id');
        $kpis = $kpisQuery->orderBy('name')->pluck('name', 'id');

        return view('masters.employee_kpi.edit', compact('assignment', 'employees', 'kpis'));
    }

    public function update(Request $request, int $id)
    {
        $assignment = DB::table('employee_kpi_assignments')->find($id);
        abort_unless($assignment, 404);

        // Verify access
        $employee = Employee::find($assignment->employee_id);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this assignment.');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'kpi_id' => 'required|integer|exists:kpi_master,id',
            'target' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Verify new selections if changed
        if ($data['employee_id'] != $assignment->employee_id) {
            $newEmployee = Employee::find($data['employee_id']);
            if (Auth::user()->company_id && $newEmployee->company_id != Auth::user()->company_id) {
                abort(403, 'Invalid employee selected.');
            }
        }
        if ($data['kpi_id'] != $assignment->kpi_id) {
            $newKpi = KpiMaster::find($data['kpi_id']);
            if (Auth::user()->company_id && $newKpi->company_id != Auth::user()->company_id) {
                abort(403, 'Invalid KPI selected.');
            }
        }

        $data['updated_at'] = now();
        DB::table('employee_kpi_assignments')->where('id', $id)->update($data);

        return redirect()->route('employee-kpi.index')->with('success', 'KPI karyawan diperbarui.');
    }

    public function destroy(int $id)
    {
        $assignment = DB::table('employee_kpi_assignments')->find($id);
        if ($assignment) {
            $employee = Employee::find($assignment->employee_id);
            if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
                abort(403, 'Unauthorized access to this assignment.');
            }
            DB::table('employee_kpi_assignments')->where('id', $id)->delete();
        }
        
        return redirect()->route('employee-kpi.index')->with('success', 'KPI karyawan dihapus.');
    }
}
