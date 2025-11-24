<?php

namespace App\Http\Controllers;

use App\Services\RegularPayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RegularPayrollController extends Controller
{
    public function __construct(private RegularPayrollService $service)
    {
    }

    public function generate(int $periodId, Request $request)
    {
        $period = DB::table('payroll_periods')->find($periodId);
        abort_unless($period, 404, 'Payroll period not found.');

        if ($request->user()?->company_id && $request->user()->company_id !== (int) $period->company_id) {
            abort(403, 'Company scope mismatch.');
        }

        $this->service->generateForPeriod($periodId);
        return response()->json(['status' => 'ok', 'payroll_period_id' => $periodId]);
    }
}
