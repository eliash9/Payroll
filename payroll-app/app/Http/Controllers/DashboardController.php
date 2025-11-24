<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin dashboard: top fundraiser, ringkasan donasi, jam aktif, estimasi komisi.
     */
    public function admin(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $campaign = $request->input('campaign');

        $start = Carbon::parse("{$period}-01")->startOfDay();
        $end = (clone $start)->endOfMonth();

        $query = DB::table('fundraising_transactions as ft')
            ->join('employees as e', 'e.id', '=', 'ft.fundraiser_id')
            ->whereBetween('ft.date_received', [$start, $end]);

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
            $hours = DB::table('attendance_summaries')
                ->select('employee_id')
                ->selectRaw('SUM(worked_minutes)/60 as total_hours')
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                ->whereIn('employee_id', $fundraiserIds)
                ->groupBy('employee_id')
                ->get()
                ->keyBy('employee_id');
        }

        // Estimasi komisi berdasarkan rate & cap per employee
        $employees = DB::table('employees')
            ->whereIn('id', $fundraiserIds)
            ->get()
            ->keyBy('id');

        $topFundraisers = $topFundraisers->map(function ($row) use ($hours, $employees) {
            $emp = $employees->get($row->fundraiser_id);
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

        return view('volunteer', [
            'employee' => $emp,
            'period' => $period,
            'donation' => $donation,
            'totalHours' => $totalHours,
            'hourlyIncome' => $hourlyIncome,
            'commission' => $commission,
        ]);
    }
}
