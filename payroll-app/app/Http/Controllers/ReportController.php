<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function payroll(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $periodId = $request->input('period_id');
        
        $periodsQuery = PayrollPeriod::query();
        if ($companyId) {
            $periodsQuery->where('company_id', $companyId);
        }
        $periods = $periodsQuery->orderByDesc('start_date')->pluck('name', 'id');

        $reportData = collect();
        $periodName = '-';

        if ($periodId) {
            $period = PayrollPeriod::findOrFail($periodId);
            if ($companyId && $period->company_id != $companyId) {
                abort(403);
            }
            $periodName = $period->name;

            // Fetch summary data from payroll_headers
            $reportData = DB::table('payroll_headers')
                ->join('employees', 'employees.id', '=', 'payroll_headers.employee_id')
                ->where('payroll_headers.payroll_period_id', $periodId)
                ->select(
                    'employees.full_name',
                    'employees.employee_code',
                    'payroll_headers.gross_income',
                    'payroll_headers.total_deduction',
                    'payroll_headers.net_income as net_pay'
                )
                ->orderBy('employees.full_name')
                ->get();
        }

        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new ReportExport('reports.payroll_table', compact('reportData')), 'laporan_gaji.xlsx');
            } elseif ($request->export == 'pdf') {
                $data = [
                    'title' => 'Laporan Gaji',
                    'period' => $periodName,
                    'tableView' => 'reports.payroll_table',
                    'reportData' => $reportData
                ];
                $pdf = Pdf::loadView('reports.pdf', $data);
                return $pdf->download('laporan_gaji.pdf');
            }
        }

        return view('reports.payroll', compact('periods', 'reportData'));
    }

    public function attendance(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $month = $request->input('month', now()->format('Y-m'));
        
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $query = DB::table('attendance_summaries')
            ->join('employees', 'employees.id', '=', 'attendance_summaries.employee_id')
            ->whereBetween('attendance_summaries.work_date', [$start, $end])
            ->select(
                'employees.full_name',
                'employees.employee_code',
                DB::raw('COUNT(attendance_summaries.id) as total_days'),
                DB::raw('SUM(attendance_summaries.worked_minutes) as total_minutes'),
                DB::raw('SUM(CASE WHEN attendance_summaries.late_minutes > 0 THEN 1 ELSE 0 END) as late_days')
            );

        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }

        $reportData = $query->groupBy('employees.id', 'employees.full_name', 'employees.employee_code')
            ->orderBy('employees.full_name')
            ->get();

        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new ReportExport('reports.attendance_table', compact('reportData')), 'laporan_kehadiran.xlsx');
            } elseif ($request->export == 'pdf') {
                $data = [
                    'title' => 'Laporan Kehadiran',
                    'period' => Carbon::parse($month)->translatedFormat('F Y'),
                    'tableView' => 'reports.attendance_table',
                    'reportData' => $reportData
                ];
                $pdf = Pdf::loadView('reports.pdf', $data);
                return $pdf->download('laporan_kehadiran.pdf');
            }
        }

        return view('reports.attendance', compact('month', 'reportData'));
    }

    public function fundraising(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $month = $request->input('month', now()->format('Y-m'));
        
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $query = DB::table('fundraising_transactions')
            ->join('employees', 'employees.id', '=', 'fundraising_transactions.fundraiser_id')
            ->whereBetween('fundraising_transactions.date_received', [$start, $end])
            ->select(
                'employees.full_name',
                'employees.employee_code',
                DB::raw('COUNT(fundraising_transactions.id) as total_transactions'),
                DB::raw('SUM(fundraising_transactions.amount) as total_amount')
            );

        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }

        $reportData = $query->groupBy('employees.id', 'employees.full_name', 'employees.employee_code')
            ->orderByDesc('total_amount')
            ->get();

        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new ReportExport('reports.fundraising_table', compact('reportData')), 'laporan_fundraising.xlsx');
            } elseif ($request->export == 'pdf') {
                $data = [
                    'title' => 'Laporan Fundraising',
                    'period' => Carbon::parse($month)->translatedFormat('F Y'),
                    'tableView' => 'reports.fundraising_table',
                    'reportData' => $reportData
                ];
                $pdf = Pdf::loadView('reports.pdf', $data);
                return $pdf->download('laporan_fundraising.pdf');
            }
        }

        return view('reports.fundraising', compact('month', 'reportData'));
    }
}
