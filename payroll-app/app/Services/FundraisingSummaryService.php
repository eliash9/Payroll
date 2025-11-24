<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FundraisingSummaryService
{
    /**
     * Generate daily summaries for a date range (default: yesterday).
     */
    public function summarize(?string $startDate = null, ?string $endDate = null, ?int $companyId = null): void
    {
        $start = Carbon::parse($startDate ?? now()->subDay()->toDateString())->startOfDay();
        $end = Carbon::parse($endDate ?? $startDate ?? now()->subDay()->toDateString())->endOfDay();

        $rows = DB::table('fundraising_transactions')
            ->selectRaw('company_id, fundraiser_id, CAST(date_received as date) as summary_date')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as total_transactions')
            ->whereBetween('date_received', [$start, $end])
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->groupBy('company_id', 'fundraiser_id', DB::raw('CAST(date_received as date)'))
            ->get();

        $this->upsertSummaries($rows);
    }

    private function upsertSummaries(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $now = now();
        $payload = $rows->map(function ($row) use ($now) {
            return [
                'company_id' => $row->company_id,
                'fundraiser_id' => $row->fundraiser_id,
                'summary_date' => $row->summary_date,
                'total_amount' => $row->total_amount,
                'total_transactions' => $row->total_transactions,
                'new_donors' => 0, // Isi jika ada data donor baru
                'repeat_donors' => 0, // Isi jika ada data donor ulang
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        DB::table('fundraising_daily_summaries')->upsert(
            $payload,
            ['company_id', 'fundraiser_id', 'summary_date'],
            ['total_amount', 'total_transactions', 'new_donors', 'repeat_donors', 'updated_at']
        );
    }
}
