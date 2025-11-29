<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSummary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $start = "{$period}-01";
        $end = now()->parse($start)->endOfMonth()->toDateString();

        // Use Eloquent or DB with scope
        // AttendanceSummary model might not exist or needs CompanyScope
        // Let's assume we use DB for performance but filter by company_id manually if needed, 
        // OR use Eloquent if we want to be clean.
        // Given the user request, let's stick to Eloquent if possible or strict DB filtering.
        
        $query = DB::table('attendance_summaries as a')
            ->join('employees as e', 'e.id', '=', 'a.employee_id')
            ->leftJoin('branches as b', 'b.id', '=', 'e.branch_id')
            ->select('a.*', 'e.full_name', 'e.employee_code', 'b.name as branch_name');

        if (Auth::user()->company_id) {
            $query->where('e.company_id', Auth::user()->company_id);
        }

        $summaries = $query->whereBetween('a.work_date', [$start, $end])
            ->orderByDesc('a.work_date')
            ->paginate(50);

        $employeesQuery = Employee::query();
        if (Auth::user()->company_id) {
            $employeesQuery->where('company_id', Auth::user()->company_id);
        }
        $employees = $employeesQuery->orderBy('full_name')->get(['id', 'full_name', 'employee_code']);

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
        
        // Verify employee belongs to company
        $employee = Employee::findOrFail($data['employee_id']);
        if (Auth::user()->company_id && $employee->company_id != Auth::user()->company_id) {
            abort(403, 'Invalid employee selected.');
        }

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
