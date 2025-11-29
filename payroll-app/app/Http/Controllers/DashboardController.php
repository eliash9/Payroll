<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Admin dashboard: General stats + Fundraising stats.
     */
    public function admin(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // --- General Stats ---
        $totalEmployees = Employee::query();
        $totalBranches = Branch::query();
        $totalDepartments = Department::query();
        $presentToday = DB::table('attendance_summaries')
            ->join('employees', 'employees.id', '=', 'attendance_summaries.employee_id')
            ->where('attendance_summaries.work_date', Carbon::today());

        if ($companyId) {
            $totalEmployees->where('company_id', $companyId);
            $totalBranches->where('company_id', $companyId);
            $totalDepartments->where('company_id', $companyId);
            $presentToday->where('employees.company_id', $companyId);
        }

        $stats = [
            'employees' => $totalEmployees->count(),
            'branches' => $totalBranches->count(),
            'departments' => $totalDepartments->count(),
            'present_today' => $presentToday->count(),
        ];

        // --- Fundraising Stats ---
        $period = $request->input('period', now()->format('Y-m'));
        $campaign = $request->input('campaign');

        $start = Carbon::parse("{$period}-01")->startOfDay();
        $end = (clone $start)->endOfMonth();

        $query = DB::table('fundraising_transactions as ft')
            ->join('employees as e', 'e.id', '=', 'ft.fundraiser_id')
            ->whereBetween('ft.date_received', [$start, $end]);

        if ($companyId) {
            $query->where('e.company_id', $companyId);
        }

        if ($campaign) {
            $query->where('ft.campaign_name', $campaign);
        }

        $topFundraisers = $query
            ->select(
                'ft.fundraiser_id',
                'e.full_name',
            )
            ->selectRaw('SUM(ft.amount) as total_amount')
            ->selectRaw('COUNT(ft.id) as total_transactions')
            ->groupBy('ft.fundraiser_id', 'e.full_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Hitung jam aktif untuk daftar fundraiser yang ada di top list
        $fundraiserIds = $topFundraisers->pluck('fundraiser_id')->all();
        $hours = collect();
        if ($fundraiserIds) {
            $hoursQuery = DB::table('attendance_summaries')
                ->join('employees', 'employees.id', '=', 'attendance_summaries.employee_id')
                ->select('attendance_summaries.employee_id')
                ->selectRaw('SUM(attendance_summaries.worked_minutes)/60 as total_hours')
                ->whereBetween('attendance_summaries.work_date', [$start->toDateString(), $end->toDateString()])
                ->whereIn('attendance_summaries.employee_id', $fundraiserIds);
            
            if ($companyId) {
                $hoursQuery->where('employees.company_id', $companyId);
            }

            $hours = $hoursQuery->groupBy('attendance_summaries.employee_id')
                ->get()
                ->keyBy('employee_id');
        }

        // Estimasi komisi berdasarkan rate & cap per employee
        $employeesQuery = DB::table('employees')->whereIn('id', $fundraiserIds);
        if ($companyId) {
            $employeesQuery->where('company_id', $companyId);
        }
        $employees = $employeesQuery->get()->keyBy('id');

        $topFundraisers = $topFundraisers->map(function ($row) use ($hours, $employees) {
            $emp = $employees->get($row->fundraiser_id);
            // If employee not found (maybe due to scoping issue if data is inconsistent), skip or default
            if (!$emp) return $row;

            $rate = $emp->commission_rate ?? 0;
            $cap = $emp->max_commission_cap ?? null;
            $commission = $row->total_amount * ($rate / 100);
            if ($cap && $commission > $cap) {
                $commission = $cap;
            }
            $row->total_hours = $hours[$row->fundraiser_id]->total_hours ?? 0;
            $row->commission = $commission;
            return $row;
        });

        $totals = [
            'amount' => $topFundraisers->sum('total_amount'),
            'transactions' => $topFundraisers->sum('total_transactions'),
            'hours' => $topFundraisers->sum('total_hours'),
            'commission' => $topFundraisers->sum('commission'),
        ];

        return view('dashboard', [
            'period' => $period,
            'campaign' => $campaign,
            'topFundraisers' => $topFundraisers,
            'totals' => $totals,
            'stats' => $stats,
            'chartLabels' => $topFundraisers->pluck('full_name'),
            'chartData' => $topFundraisers->pluck('total_amount'),
        ]);
    }

    /**
     * Relawan dashboard: ringkasan pribadi.
     */
    public function volunteer(Request $request)
    {
        $employeeId = (int) $request->input('employee_id');
        if (!$employeeId) {
            return redirect()->back()->with('error', 'employee_id diperlukan');
        }

        $period = $request->input('period', now()->format('Y-m'));
        $start = Carbon::parse("{$period}-01")->startOfDay();
        $end = (clone $start)->endOfMonth();

        $emp = DB::table('employees')->find($employeeId);
        if (!$emp) {
            return redirect()->back()->with('error', 'Relawan tidak ditemukan');
        }
        
        // Ensure volunteer belongs to user's company if user has company_id
        if (Auth::user()->company_id && $emp->company_id != Auth::user()->company_id) {
             abort(403, 'Unauthorized access to this volunteer.');
        }

        $donation = DB::table('fundraising_transactions')
            ->where('fundraiser_id', $employeeId)
            ->whereBetween('date_received', [$start, $end])
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(id) as total_transactions')
            ->first();

        $attendanceMinutes = DB::table('attendance_summaries')
            ->where('employee_id', $employeeId)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->sum('worked_minutes');
        $totalHours = $attendanceMinutes / 60;
        $hourlyIncome = $totalHours * (float) $emp->hourly_rate;

        $commission = ($donation->total_amount ?? 0) * ((float) $emp->commission_rate / 100);
        if ($emp->max_commission_cap && $commission > (float) $emp->max_commission_cap) {
            $commission = (float) $emp->max_commission_cap;
        }

        $claims = DB::table('expense_claims')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('date')
            ->get();

        return view('volunteer', [
            'employee' => $emp,
            'period' => $period,
            'donation' => $donation,
            'totalHours' => $totalHours,
            'hourlyIncome' => $hourlyIncome,
            'commission' => $commission,
            'claims' => $claims,
        ]);
    }
}
