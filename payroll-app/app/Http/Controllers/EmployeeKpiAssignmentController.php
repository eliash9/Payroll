<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeKpiAssignmentController extends Controller
{
    public function index()
    {
        $assignments = DB::table('employee_kpi_assignments')
            ->join('employees', 'employees.id', '=', 'employee_kpi_assignments.employee_id')
            ->join('kpi_master', 'kpi_master.id', '=', 'employee_kpi_assignments.kpi_id')
            ->select(
                'employee_kpi_assignments.*',
                'employees.full_name as employee_name',
                'employees.employee_code',
                'kpi_master.name as kpi_name',
                'kpi_master.code as kpi_code'
            )
            ->orderBy('employees.full_name')
            ->orderBy('kpi_master.name')
            ->paginate(20);

        return view('masters.employee_kpi.index', compact('assignments'));
    }

    public function create()
    {
        $employees = DB::table('employees')->orderBy('full_name')->pluck('full_name', 'id');
        $kpis = DB::table('kpi_master')->orderBy('name')->pluck('name', 'id');
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

        $employees = DB::table('employees')->orderBy('full_name')->pluck('full_name', 'id');
        $kpis = DB::table('kpi_master')->orderBy('name')->pluck('name', 'id');

        return view('masters.employee_kpi.edit', compact('assignment', 'employees', 'kpis'));
    }

    public function update(Request $request, int $id)
    {
        $assignment = DB::table('employee_kpi_assignments')->find($id);
        abort_unless($assignment, 404);

        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'kpi_id' => 'required|integer|exists:kpi_master,id',
            'target' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['updated_at'] = now();
        DB::table('employee_kpi_assignments')->where('id', $id)->update($data);

        return redirect()->route('employee-kpi.index')->with('success', 'KPI karyawan diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('employee_kpi_assignments')->where('id', $id)->delete();
        return redirect()->route('employee-kpi.index')->with('success', 'KPI karyawan dihapus.');
    }
}
