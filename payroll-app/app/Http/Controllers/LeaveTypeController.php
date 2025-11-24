<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = DB::table('leave_types')
            ->join('companies', 'companies.id', '=', 'leave_types.company_id')
            ->select('leave_types.*', 'companies.name as company_name')
            ->orderBy('leave_types.name')
            ->paginate(20);

        return view('masters.leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.leave_types.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:leave_types,code',
            'is_paid' => 'boolean',
            'is_annual_quota' => 'boolean',
            'default_quota_days' => 'nullable|numeric|min:0',
        ]);

        $now = now();
        DB::table('leave_types')->insert(array_merge([
            'is_paid' => false,
            'is_annual_quota' => false,
        ], $data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin ditambahkan.');
    }

    public function edit(int $id)
    {
        $leaveType = DB::table('leave_types')->find($id);
        abort_unless($leaveType, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.leave_types.edit', compact('leaveType', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $leaveType = DB::table('leave_types')->find($id);
        abort_unless($leaveType, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:leave_types,code,' . $id,
            'is_paid' => 'boolean',
            'is_annual_quota' => 'boolean',
            'default_quota_days' => 'nullable|numeric|min:0',
        ]);

        $data['is_paid'] = $request->boolean('is_paid');
        $data['is_annual_quota'] = $request->boolean('is_annual_quota');
        $data['updated_at'] = now();

        DB::table('leave_types')->where('id', $id)->update($data);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('leave_types')->where('id', $id)->delete();
        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin dihapus.');
    }
}
