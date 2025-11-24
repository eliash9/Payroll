<?php

namespace App\Http\Controllers;

use App\Services\FundraisingSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FundraisingSummaryController extends Controller
{
    public function __construct(private FundraisingSummaryService $summaryService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user?->company_id ?? $request->integer('company_id');
        $period = $request->input('period'); // format YYYY-MM

        if (!$companyId) {
            abort(400, 'company_id is required for scoped access.');
        }

        if ($period) {
            $start = "{$period}-01";
            $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateString();

            $data = DB::table('fundraising_daily_summaries')
                ->select('company_id', 'fundraiser_id')
                ->selectRaw('SUM(total_amount) as total_amount')
                ->selectRaw('SUM(total_transactions) as total_transactions')
                ->where('company_id', $companyId)
                ->whereBetween('summary_date', [$start, $end])
                ->groupBy('company_id', 'fundraiser_id')
                ->get();

            return response()->json(['period' => $period, 'data' => $data]);
        }

        $data = DB::table('fundraising_daily_summaries')
            ->where('company_id', $companyId)
            ->orderByDesc('summary_date')
            ->limit(100)
            ->get();

        return response()->json($data);
    }

    public function generate(Request $request)
    {
        $user = $request->user();
        $companyId = $user?->company_id ?? $request->integer('company_id');
        if (!$companyId) {
            abort(400, 'company_id is required for scoped access.');
        }

        $this->summaryService->summarize($request->input('start'), $request->input('end'), $companyId);
        return response()->json(['status' => 'ok']);
    }
}
