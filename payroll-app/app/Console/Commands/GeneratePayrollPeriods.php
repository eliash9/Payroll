<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePayrollPeriods extends Command
{
    protected $signature = 'payroll:generate-periods {company_id} {--start= : YYYY-MM (default: bulan ini)} {--months=12 : Jumlah bulan yang akan dibuat}';

    protected $description = 'Generate payroll_periods berurutan untuk suatu company';

    public function handle(): int
    {
        $companyId = (int) $this->argument('company_id');
        $months = (int) $this->option('months');
        $startYm = $this->option('start') ?: now()->format('Y-m');

        $start = Carbon::createFromFormat('Y-m', $startYm)->startOfMonth();

        for ($i = 0; $i < $months; $i++) {
            $code = $start->format('Y-m');
            $exists = DB::table('payroll_periods')->where('company_id', $companyId)->where('code', $code)->exists();
            if ($exists) {
                $this->line("Skip {$code} (sudah ada)");
            } else {
                DB::table('payroll_periods')->insert([
                    'company_id' => $companyId,
                    'code' => $code,
                    'name' => $start->translatedFormat('F Y'),
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->clone()->endOfMonth()->toDateString(),
                    'status' => 'draft',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("Buat periode {$code}");
            }
            $start->addMonth();
        }

        return Command::SUCCESS;
    }
}
