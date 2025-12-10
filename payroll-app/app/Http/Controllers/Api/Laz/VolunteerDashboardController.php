<?php

namespace App\Http\Controllers\Api\Laz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FundraisingTransaction;

class VolunteerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Check if user has employee record
        $employee = $user->employee;
        
        if (!$employee || !$employee->is_volunteer) {
             return response()->json([
                'is_volunteer' => false,
                'message' => 'User is not a volunteer'
             ]);
        }

        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // Today's Stats
        $todayStats = DB::table('fundraising_transactions')
            ->where('fundraiser_id', $employee->id)
            ->whereDate('date_received', $today)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount, COUNT(*) as total_count')
            ->first();

        // Month's Stats
        $monthStats = DB::table('fundraising_transactions')
            ->where('fundraiser_id', $employee->id)
            ->whereBetween('date_received', [$startOfMonth, $endOfMonth])
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount, COUNT(*) as total_count')
            ->first();
            
        // Calculate Rank (Simple internal ranking within company for this month)
        // This is expensive if many users, but fine for now.
        $rank = DB::table('fundraising_transactions')
             ->where('company_id', $employee->company_id)
             ->whereBetween('date_received', [$startOfMonth, $endOfMonth])
             ->select('fundraiser_id', DB::raw('SUM(amount) as total'))
             ->groupBy('fundraiser_id')
             ->orderByDesc('total')
             ->get();
        
        $myRank = $rank->search(function ($item) use ($employee) {
            return $item->fundraiser_id == $employee->id;
        });
        
        $myRank = $myRank !== false ? $myRank + 1 : '-';

        return response()->json([
            'is_volunteer' => true,
            'stats' => [
                'today' => [
                    'amount' => $todayStats->total_amount,
                    'count' => $todayStats->total_count
                ],
                'month' => [
                    'amount' => $monthStats->total_amount,
                    'count' => $monthStats->total_count
                ],
                'rank' => $myRank
            ]
        ]);
    }
}
