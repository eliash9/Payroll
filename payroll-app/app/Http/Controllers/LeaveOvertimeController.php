<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveOvertimeController extends Controller
{
    public function index()
    {
        $employees = DB::table('employees')->orderBy('full_name')->get(['id', 'full_name', 'employee_code']);
        $leaveTypes = DB::table('leave_types')->orderBy('name')->get(['id', 'name']);

        $leaves = DB::table('leave_requests as l')
            ->join('employees as e', 'e.id', '=', 'l.employee_id')
            ->select('l.*', 'e.full_name', 'e.employee_code')
            ->orderByDesc('l.created_at')
            ->limit(50)
            ->get();

        $overtimes = DB::table('overtime_requests as o')
            ->join('employees as e', 'e.id', '=', 'o.employee_id')
            ->select('o.*', 'e.full_name', 'e.employee_code')
            ->orderByDesc('o.created_at')
            ->limit(50)
            ->get();

        return view('leave_overtime.index', compact('employees', 'leaveTypes', 'leaves', 'overtimes'));
    }

    public function storeLeave(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'leave_type_id' => 'required|integer|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days' => 'required|numeric',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'nullable|string',
        ]);
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('leave_requests')->insert($data);
        $this->syncAttendanceForLeave($data['employee_id'], $data['start_date'], $data['end_date'], $data['status']);
        return back()->with('success', 'Leave request saved');
    }

    public function storeOvertime(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'total_minutes' => 'required|integer',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'nullable|string',
        ]);
        $data['start_time'] = "{$data['work_date']} {$data['start_time']}";
        $data['end_time'] = "{$data['work_date']} {$data['end_time']}";
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('overtime_requests')->insert($data);
        return back()->with('success', 'Overtime request saved');
    }

    public function updateLeaveStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);
        $leave = DB::table('leave_requests')->find($id);
        DB::table('leave_requests')->where('id', $id)->update([
            'status' => $data['status'],
            'updated_at' => now(),
        ]);
        if ($leave) {
            $this->syncAttendanceForLeave($leave->employee_id, $leave->start_date, $leave->end_date, $data['status']);
        }
        return back()->with('success', 'Status cuti diperbarui');
    }

    public function updateOvertimeStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);
        DB::table('overtime_requests')->where('id', $id)->update([
            'status' => $data['status'],
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Status lembur diperbarui');
    }

    private function syncAttendanceForLeave(int $employeeId, string $startDate, string $endDate, string $status): void
    {
        if ($status !== 'approved') {
            return;
        }
        $dates = collect();
        $cursor = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        while ($cursor->lte($end)) {
            $dates->push($cursor->toDateString());
            $cursor->addDay();
        }

        foreach ($dates as $date) {
            DB::table('attendance_summaries')->updateOrInsert(
                ['employee_id' => $employeeId, 'work_date' => $date],
                [
                    'status' => 'leave',
                    'worked_minutes' => 0,
                    'late_minutes' => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
