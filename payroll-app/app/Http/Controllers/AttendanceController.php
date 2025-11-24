<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $start = "{$period}-01";
        $end = now()->parse($start)->endOfMonth()->toDateString();

        $summaries = DB::table('attendance_summaries as a')
            ->join('employees as e', 'e.id', '=', 'a.employee_id')
            ->select('a.*', 'e.full_name', 'e.employee_code')
            ->whereBetween('a.work_date', [$start, $end])
            ->orderByDesc('a.work_date')
            ->paginate(50);

        $employees = DB::table('employees')->orderBy('full_name')->get(['id', 'full_name', 'employee_code']);

        return view('attendance.index', compact('period', 'summaries', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'type' => 'required|in:in,out',
            'work_date' => 'required|date',
            'time' => 'required',
            'worked_minutes' => 'nullable|integer',
        ]);

        $dateTime = "{$data['work_date']} {$data['time']}";
        $update = ['updated_at' => now()];

        if ($data['type'] === 'in') {
            $update['check_in'] = $dateTime;
        } else {
            $update['check_out'] = $dateTime;
        }

        if (isset($data['worked_minutes'])) {
            $update['worked_minutes'] = $data['worked_minutes'];
        }

        DB::table('attendance_summaries')->updateOrInsert(
            ['employee_id' => $data['employee_id'], 'work_date' => $data['work_date']],
            $update + ['status' => 'present', 'created_at' => now()]
        );

        return back()->with('success', 'Absensi tersimpan');
    }
}
