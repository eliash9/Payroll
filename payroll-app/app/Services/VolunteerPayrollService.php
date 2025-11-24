<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class VolunteerPayrollService
{
    /**
     * Generate payroll for volunteers (is_volunteer=1) in a payroll_period.
     */
    public function generateForPeriod(int $payrollPeriodId): void
    {
        $period = DB::table('payroll_periods')->find($payrollPeriodId);
        if (!$period) {
            throw new RuntimeException('Payroll period not found.');
        }

        $components = DB::table('payroll_components')
            ->where('company_id', $period->company_id)
            ->whereIn('code', ['HOURLY_INCOME', 'FUNDRAISING_COMMISSION', 'TARGET_BONUS'])
            ->pluck('id', 'code');

        if ($components->count() < 3) {
            throw new RuntimeException('Required payroll components missing. Seed them first.');
        }

        $start = Carbon::parse($period->start_date)->startOfDay();
        $end = Carbon::parse($period->end_date)->endOfDay();

        $volunteers = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', true)
            ->where('status', 'active')
            ->get();

        foreach ($volunteers as $volunteer) {
            DB::transaction(function () use ($volunteer, $period, $components, $start, $end) {
                $attendanceMinutes = DB::table('attendance_summaries')
                    ->where('employee_id', $volunteer->id)
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->sum('worked_minutes');

                $totalHours = $attendanceMinutes / 60;
                $hourlyIncome = $totalHours * (float) $volunteer->hourly_rate;

                $totalDonation = DB::table('fundraising_transactions')
                    ->where('fundraiser_id', $volunteer->id)
                    ->whereBetween('date_received', [$start, $end])
                    ->sum('amount');

                $commission = $totalDonation * ((float) $volunteer->commission_rate / 100);
                if ($volunteer->max_commission_cap && $commission > (float) $volunteer->max_commission_cap) {
                    $commission = (float) $volunteer->max_commission_cap;
                }

                $kpiBonus = $this->calculateKpiBonus($volunteer->id, $start, $end);
                $kpiComponents = $this->getKpiComponents($volunteer->id, $start, $end, $period->company_id);
                $kpiTotal = array_sum(array_column($kpiComponents, 'amount'));

                $gross = $hourlyIncome + $commission + $kpiBonus + $kpiTotal;
                $net = $gross; // no deduction for volunteers by default

                $headerId = DB::table('payroll_headers')->updateOrInsert(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_id' => $volunteer->id,
                    ],
                    [
                        'gross_income' => $gross,
                        'total_deduction' => 0,
                        'net_income' => $net,
                        'status' => 'calculated',
                        'generated_at' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $header = DB::table('payroll_headers')
                    ->where('payroll_period_id', $period->id)
                    ->where('employee_id', $volunteer->id)
                    ->first();

                // Reset details
                DB::table('payroll_details')->where('payroll_header_id', $header->id)->delete();

                $detailRows = [
                    [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['HOURLY_INCOME'],
                        'amount' => $hourlyIncome,
                        'quantity' => $totalHours,
                        'remark' => 'Hourly income from attendance',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['FUNDRAISING_COMMISSION'],
                        'amount' => $commission,
                        'quantity' => $totalDonation,
                        'remark' => 'Commission from fundraising',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['TARGET_BONUS'],
                        'amount' => $kpiBonus,
                        'quantity' => null,
                        'remark' => 'KPI bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];

                foreach ($kpiComponents as $kpiComp) {
                    if (isset($components[$kpiComp['code']])) {
                        $detailRows[] = [
                            'payroll_header_id' => $header->id,
                            'payroll_component_id' => $components[$kpiComp['code']],
                            'amount' => $kpiComp['amount'],
                            'quantity' => null,
                            'remark' => $kpiComp['label'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                DB::table('payroll_details')->insert($detailRows);

                // Lock attendance rows for the period
                DB::table('attendance_summaries')
                    ->where('employee_id', $volunteer->id)
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->update(['locked' => true, 'updated_at' => now()]);
            });
        }
    }

    private function calculateKpiBonus(int $employeeId, Carbon $start, Carbon $end): float
    {
        $rows = DB::table('employee_kpi_results')
            ->join('kpi_payroll_mapping', 'kpi_payroll_mapping.kpi_id', '=', 'employee_kpi_results.kpi_id')
            ->select('employee_kpi_results.achievement_percentage', 'employee_kpi_results.actual_value', 'kpi_payroll_mapping.max_amount')
            ->where('employee_kpi_results.employee_id', $employeeId)
            ->whereBetween('period_start', [$start->toDateString(), $end->toDateString()])
            ->whereBetween('period_end', [$start->toDateString(), $end->toDateString()])
            ->get();

        $bonus = 0.0;
        foreach ($rows as $row) {
            if ($row->achievement_percentage >= 100) {
                if ($row->max_amount) {
                    $bonus += ($row->achievement_percentage / 100) * (float) $row->max_amount;
                } else {
                    $bonus += (float) $row->actual_value;
                }
            }
        }

        return $bonus;
    }

    /**
     * Simulasi payroll relawan tanpa menulis ke database.
     */
    public function simulateForPeriod(int $payrollPeriodId): array
    {
        $period = DB::table('payroll_periods')->find($payrollPeriodId);
        if (!$period) {
            throw new RuntimeException('Payroll period not found.');
        }

        $start = Carbon::parse($period->start_date)->startOfDay();
        $end = Carbon::parse($period->end_date)->endOfDay();

        $volunteers = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', true)
            ->where('status', 'active')
            ->get();

        $results = [];

        foreach ($volunteers as $volunteer) {
            $attendanceMinutes = DB::table('attendance_summaries')
                ->where('employee_id', $volunteer->id)
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                ->sum('worked_minutes');

            $totalHours = $attendanceMinutes / 60;
            $hourlyIncome = $totalHours * (float) $volunteer->hourly_rate;

            $totalDonation = DB::table('fundraising_transactions')
                ->where('fundraiser_id', $volunteer->id)
                ->whereBetween('date_received', [$start, $end])
                ->sum('amount');

            $commission = $totalDonation * ((float) $volunteer->commission_rate / 100);
            if ($volunteer->max_commission_cap && $commission > (float) $volunteer->max_commission_cap) {
                $commission = (float) $volunteer->max_commission_cap;
            }

            $kpiBonus = $this->calculateKpiBonus($volunteer->id, $start, $end);

            $gross = $hourlyIncome + $commission + $kpiBonus;
            $net = $gross;

            $components = [
                ['code' => 'HOURLY_INCOME', 'label' => 'Hourly Income', 'amount' => $hourlyIncome, 'quantity' => $totalHours],
                ['code' => 'FUNDRAISING_COMMISSION', 'label' => 'Komisi Fundraising', 'amount' => $commission, 'quantity' => $totalDonation],
                ['code' => 'TARGET_BONUS', 'label' => 'Bonus KPI', 'amount' => $kpiBonus, 'quantity' => null],
            ];

            $results[] = [
                'employee' => $volunteer,
                'gross' => $gross,
                'deduction' => 0,
                'net' => $net,
                'components' => $components,
            ];
        }

        return $results;
    }

    private function getKpiComponents(int $employeeId, Carbon $start, Carbon $end, int $companyId): array
    {
        $rows = DB::table('employee_kpi_results as r')
            ->join('kpi_payroll_mapping as m', 'm.kpi_id', '=', 'r.kpi_id')
            ->join('payroll_components as pc', 'pc.id', '=', 'm.payroll_component_id')
            ->where('r.employee_id', $employeeId)
            ->whereBetween('r.period_start', [$start->toDateString(), $end->toDateString()])
            ->whereBetween('r.period_end', [$start->toDateString(), $end->toDateString()])
            ->where('pc.company_id', $companyId)
            ->select('r.actual_value', 'r.achievement_percentage', 'm.max_amount', 'm.formula', 'pc.code', 'pc.name', 'pc.type')
            ->get();

        return $rows->map(function ($row) {
            $amount = $this->evaluateKpiAmount($row->formula, (float) $row->actual_value, (float) $row->achievement_percentage);
            if ($row->max_amount && $amount > (float) $row->max_amount) {
                $amount = (float) $row->max_amount;
            }
            return [
                'code' => $row->code,
                'label' => $row->name . ' (KPI)',
                'type' => $row->type,
                'amount' => $amount,
            ];
        })->all();
    }

    private function evaluateKpiAmount(?string $formula, float $actual, float $achievement): float
    {
        if (!$formula) {
            return $achievement >= 100 ? $actual : 0;
        }

        $allowed = '/[^0-9\+\-\*\/\.\(\)\sactualachievement]/i';
        if (preg_match($allowed, str_replace(['actual', 'achievement'], '', $formula))) {
            return 0;
        }

        $expr = str_replace(['actual', 'achievement'], [$actual, $achievement], $formula);
        try {
            $result = eval("return {$expr};");
            return is_numeric($result) ? (float) $result : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
