<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function allowedLocations(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        $locations = [];
        // Branch
        if ($employee->branch) {
            $locations[] = [
                'name' => $employee->branch->name . ' (Cabang)',
                'latitude' => $employee->branch->latitude,
                'longitude' => $employee->branch->longitude,
                'radius' => 5000 // default branch radius
            ];
        }

        // Custom Locations
        foreach ($employee->workLocations as $loc) {
            $locations[] = [
                'name' => $loc->name,
                'latitude' => $loc->latitude,
                'longitude' => $loc->longitude,
                'radius' => $loc->radius
            ];
        }

        return response()->json(['data' => $locations]);
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'required|date',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        // Validate Location (Branch Radius OR Custom Work Locations)
        $isValidLocation = false;
        $maxRadius = 5000; // Default Branch Radius
        
        // 1. Check Branch Location
        if ($employee->branch && $employee->branch->latitude && $employee->branch->longitude) {
            $dist = $this->calculateDistance($request->latitude, $request->longitude, $employee->branch->latitude, $employee->branch->longitude);
            if ($dist <= $maxRadius) {
                $isValidLocation = true;
            }
        }

        // 2. Check Custom Work Locations
        if (!$isValidLocation) {
            $customLocations = $employee->workLocations; // Assuming relationship is loaded or accessible
            foreach ($customLocations as $loc) {
                $dist = $this->calculateDistance($request->latitude, $request->longitude, $loc->latitude, $loc->longitude);
                if ($dist <= $loc->radius) {
                    $isValidLocation = true;
                    break;
                }
            }
        }

        if (!$isValidLocation) {
            return response()->json(['message' => 'Anda berada di luar jangkauan lokasi kerja (Cabang / Lokasi Custom).'], 403);
        }

        $timestamp = Carbon::parse($request->timestamp)->setTimezone('Asia/Jakarta');
        $today = $timestamp->copy()->startOfDay();

        // Check latest log for today
        $lastLog = AttendanceLog::where('employee_id', $employee->id)
            ->where('scan_time', '>=', $today)
            ->orderBy('scan_time', 'desc')
            ->first();

        if ($lastLog && $lastLog->scan_type === 'in') {
            return response()->json(['message' => 'Already clocked in. Please clock out first.'], 400);
        }

        $log = AttendanceLog::create([
            'employee_id' => $employee->id,
            'device_id' => 'mobile-pwa',
            'scan_time' => $timestamp,
            'scan_type' => 'in',
            'source' => 'mobile',
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'photo_path' => null,
        ]);

        $this->updateDailySummary($employee->id, $today);

        return response()->json([
            'message' => 'Clock in successful',
            'data' => $log
        ]);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'required|date',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        $timestamp = Carbon::parse($request->timestamp)->setTimezone('Asia/Jakarta');
        $today = $timestamp->copy()->startOfDay();

        // Check latest log for today
        $lastLog = AttendanceLog::where('employee_id', $employee->id)
            ->where('scan_time', '>=', $today)
            ->orderBy('scan_time', 'desc')
            ->first();

        if (!$lastLog || $lastLog->scan_type === 'out') {
             return response()->json(['message' => 'You must clock in first.'], 400);
        }

        $log = AttendanceLog::create([
            'employee_id' => $employee->id,
            'device_id' => 'mobile-pwa',
            'scan_time' => $timestamp,
            'scan_type' => 'out',
            'source' => 'mobile',
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ]);

        $this->updateDailySummary($employee->id, $today);

        return response()->json([
            'message' => 'Clock out successful',
            'data' => $log
        ]);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        // Return raw logs for detailed history view in PWA
        $logs = AttendanceLog::where('employee_id', $employee->id)
            ->orderBy('scan_time', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'type' => $log->scan_type === 'in' ? 'clock_in' : 'clock_out',
                    'timestamp' => $log->scan_time->toIso8601String(),
                    'date' => $log->scan_time->format('Y-m-d'),
                    'time' => $log->scan_time->format('H:i:s'),
                    'latitude' => $log->lat,
                    'longitude' => $log->lng,
                ];
            });

        return response()->json(['data' => $logs]);
    }

    public function summary(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        $query = \App\Models\AttendanceSummary::where('employee_id', $employee->id);

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('work_date', $request->month)
                  ->whereYear('work_date', $request->year);
        } else {
             $query->whereMonth('work_date', now()->month)
                   ->whereYear('work_date', now()->year);
        }

        $summaries = $query->orderBy('work_date', 'desc')->get();

        return response()->json(['data' => $summaries]);
    }

    private function updateDailySummary($employeeId, $date)
    {
        $logs = AttendanceLog::where('employee_id', $employeeId)
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            return;
        }

        $firstIn = $logs->where('scan_type', 'in')->first();
        $lastOut = $logs->where('scan_type', 'out')->last();

        // Calculate total worked minutes
        $totalMinutes = 0;
        $lastInTime = null;

        foreach ($logs as $log) {
            if ($log->scan_type === 'in') {
                $lastInTime = $log->scan_time;
            } elseif ($log->scan_type === 'out' && $lastInTime) {
                $minutes = $lastInTime->diffInMinutes($log->scan_time);
                $totalMinutes += $minutes;
                $lastInTime = null; // Reset for next pair
            }
        }

        // Update or Create Summary
        \App\Models\AttendanceSummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'work_date' => $date->format('Y-m-d'),
            ],
            [
                'check_in' => $firstIn ? $firstIn->scan_time : null,
                'check_out' => $lastOut ? $lastOut->scan_time : null,
                'worked_minutes' => $totalMinutes,
                'status' => $totalMinutes > 0 ? 'present' : 'absent', // Simplified status logic
                // 'shift_id' => ... (would need shift logic here)
            ]
        );
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $meters = $miles * 1609.344;
        return $meters;
    }
}
