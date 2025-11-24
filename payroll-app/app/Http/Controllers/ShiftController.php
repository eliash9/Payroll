<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = DB::table('shifts')
            ->join('companies', 'companies.id', '=', 'shifts.company_id')
            ->select('shifts.*', 'companies.name as company_name')
            ->orderBy('shifts.name')
            ->paginate(20);

        return view('masters.shifts.index', compact('shifts'));
    }

    public function create()
    {
        $companies = DB::table('companies')->pluck('name', 'id');
        return view('masters.shifts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:shifts,code',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'tolerance_late_minutes' => 'nullable|integer|min:0',
            'tolerance_early_leave_minutes' => 'nullable|integer|min:0',
            'is_night_shift' => 'boolean',
        ]);

        $now = now();
        DB::table('shifts')->insert(array_merge([
            'tolerance_late_minutes' => 0,
            'tolerance_early_leave_minutes' => 0,
            'is_night_shift' => false,
        ], $data, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('shifts.index')->with('success', 'Shift ditambahkan.');
    }

    public function edit(int $id)
    {
        $shift = DB::table('shifts')->find($id);
        abort_unless($shift, 404);
        $companies = DB::table('companies')->pluck('name', 'id');

        return view('masters.shifts.edit', compact('shift', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $shift = DB::table('shifts')->find($id);
        abort_unless($shift, 404);

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:shifts,code,' . $id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'tolerance_late_minutes' => 'nullable|integer|min:0',
            'tolerance_early_leave_minutes' => 'nullable|integer|min:0',
            'is_night_shift' => 'boolean',
        ]);

        $data['tolerance_late_minutes'] = $data['tolerance_late_minutes'] ?? 0;
        $data['tolerance_early_leave_minutes'] = $data['tolerance_early_leave_minutes'] ?? 0;
        $data['is_night_shift'] = $request->boolean('is_night_shift');
        $data['updated_at'] = now();

        DB::table('shifts')->where('id', $id)->update($data);

        return redirect()->route('shifts.index')->with('success', 'Shift diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('shifts')->where('id', $id)->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift dihapus.');
    }
}
