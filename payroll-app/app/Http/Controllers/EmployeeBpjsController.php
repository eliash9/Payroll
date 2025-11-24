<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeBpjsController extends Controller
{
    public function index()
    {
        $bpjs = DB::table('employee_bpjs')
            ->join('employees', 'employees.id', '=', 'employee_bpjs.employee_id')
            ->select('employee_bpjs.*', 'employees.full_name', 'employees.employee_code')
            ->orderBy('employees.full_name')
            ->paginate(20);

        return view('transactions.employee_bpjs.index', compact('bpjs'));
    }

    public function create()
    {
        $employees = DB::table('employees')->orderBy('full_name')->pluck('full_name', 'id');
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
        $employees = DB::table('employees')->orderBy('full_name')->pluck('full_name', 'id');

        return view('transactions.employee_bpjs.edit', compact('bpjs', 'employees'));
    }

    public function update(Request $request, int $id)
    {
        $bpjs = DB::table('employee_bpjs')->find($id);
        abort_unless($bpjs, 404);

        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'bpjs_kesehatan_number' => 'nullable|string|max:50',
            'bpjs_kesehatan_class' => 'nullable|string|max:10',
            'bpjs_ketenagakerjaan_number' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['updated_at'] = now();

        DB::table('employee_bpjs')->where('id', $id)->update($data);

        return redirect()->route('employee-bpjs.index')->with('success', 'Data BPJS karyawan diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('employee_bpjs')->where('id', $id)->delete();
        return redirect()->route('employee-bpjs.index')->with('success', 'Data BPJS karyawan dihapus.');
    }
}
