<?php

namespace App\Http\Controllers;

use App\Services\VolunteerPayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VolunteerPayrollController extends Controller
{
    public function __construct(private VolunteerPayrollService $service)
    {
    }

    public function generate(int $periodId, Request $request)
    {
        $period = DB::table('payroll_periods')->find($periodId);
        abort_unless($period, 404, 'Payroll period not found.');

        $this->assertCompanyAccess($request->user()?->company_id, (int) $period->company_id);
        $this->service->generateForPeriod($periodId);
        return response()->json(['status' => 'ok', 'payroll_period_id' => $periodId]);
    }

    private function assertCompanyAccess(?int $userCompanyId, int $periodCompanyId): void
    {
        if ($userCompanyId && $userCompanyId !== $periodCompanyId) {
            abort(403, 'Company scope mismatch.');
        }
    }
}
