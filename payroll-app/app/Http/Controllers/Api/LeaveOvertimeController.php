<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;
use App\Models\Employee;

class LeaveOvertimeController extends Controller
{
    public function getLeaveTypes()
    {
        $user = Auth::user();
        $query = LeaveType::query();
        
        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        $types = $query->orderBy('name')->get(['id', 'name', 'code', 'default_quota_days']);
        return response()->json(['data' => $types]);
    }

    public function getLeaves(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $leaves = DB::table('leave_requests as l')
            ->join('leave_types as lt', 'lt.id', '=', 'l.leave_type_id')
            ->select('l.*', 'lt.name as leave_type_name')
            ->where('l.employee_id', $employee->id)
            ->orderByDesc('l.created_at')
            ->limit(20)
            ->get();

        return response()->json(['data' => $leaves]);
    }

    public function storeLeave(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $data = $request->validate([
            'leave_type_id' => 'required|integer|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        // Calculate total days
        $start = \Carbon\Carbon::parse($data['start_date']);
        $end = \Carbon\Carbon::parse($data['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $leaveId = DB::table('leave_requests')->insertGetId([
            'employee_id' => $employee->id,
            'leave_type_id' => $data['leave_type_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $totalDays,
            'reason' => $data['reason'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Pengajuan cuti berhasil disimpan', 'id' => $leaveId]);
    }

    public function getOvertimes(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $overtimes = DB::table('overtime_requests')
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json(['data' => $overtimes]);
    }

    public function storeOvertime(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $data = $request->validate([
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string',
        ]);

        // Calculate total minutes
        $start = \Carbon\Carbon::parse("{$data['work_date']} {$data['start_time']}");
        $end = \Carbon\Carbon::parse("{$data['work_date']} {$data['end_time']}");
        
        if ($end->lessThan($start)) {
            $end->addDay(); // Handle overnight overtime if applicable, though simplistic
        }

        $totalMinutes = $start->diffInMinutes($end);

        $overtimeId = DB::table('overtime_requests')->insertGetId([
            'employee_id' => $employee->id,
            'work_date' => $data['work_date'],
            'start_time' => $start,
            'end_time' => $end,
            'total_minutes' => $totalMinutes,
            'reason' => $data['reason'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Pengajuan lembur berhasil disimpan', 'id' => $overtimeId]);
    }
}
