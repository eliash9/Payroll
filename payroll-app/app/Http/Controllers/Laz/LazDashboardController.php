<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Disbursement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LazDashboardController extends Controller
{
    public function index(): View
    {
        $totalApplications = Application::count();
        $monthApplications = Application::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $approved = Application::where('status', 'approved')->count();
        $rejected = Application::where('status', 'rejected')->count();

        $sumRequested = Application::sum('requested_amount');
        $sumApproved = DB::table('approvals')->sum('approved_amount');
        $sumDisbursed = Disbursement::sum('total_amount');

        $perProgram = Application::select('program_id', DB::raw('count(*) as total'))
            ->with('program')
            ->groupBy('program_id')
            ->get();

        $perStatus = Application::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return view('laz.dashboard', compact(
            'totalApplications',
            'monthApplications',
            'approved',
            'rejected',
            'sumRequested',
            'sumApproved',
            'sumDisbursed',
            'perProgram',
            'perStatus'
        ));
    }
}
