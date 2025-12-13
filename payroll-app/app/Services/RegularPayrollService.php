<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RegularPayrollService
{
    public function generateForPeriod(int $payrollPeriodId): void
    {
        $period = DB::table('payroll_periods')->find($payrollPeriodId);
        if (!$period) {
            throw new RuntimeException('Payroll period not found');
        }

        $start = Carbon::parse($period->start_date)->startOfDay();
        $end = Carbon::parse($period->end_date)->endOfDay();

        $bpjsRates = $this->getActiveBpjsRates($period->company_id, $period->end_date);
        $taxRates = $this->getActiveTaxRates($period->company_id, $period->end_date);

        $employees = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', false)
            ->where('status', 'active')
            ->get();

        foreach ($employees as $emp) {
            DB::transaction(function () use ($emp, $period, $bpjsRates, $taxRates, $start, $end) {
                $overtimeMinutes = DB::table('overtime_requests')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->whereBetween('work_date', [$period->start_date, $period->end_date])
                    ->sum('total_minutes');

                // Calculate Attendance Stats
                $attendanceStats = DB::table('attendance_summaries')
                    ->where('employee_id', $emp->id)
                    ->whereBetween('work_date', [$period->start_date, $period->end_date])
                    ->selectRaw('count(*) as days, sum(worked_minutes) as minutes')
                    ->first();
                $daysPresent = $attendanceStats->days ?? 0;
                $totalWorkedHours = ($attendanceStats->minutes ?? 0) / 60;

                $hourlyRate = ($emp->basic_salary / 173);
                $overtimePay = $this->calculateOvertimePay($overtimeMinutes, $hourlyRate);
                $gross = (float) $emp->basic_salary + $overtimePay;

                $componentItems = $this->getEmployeeComponents($emp->id, $period->start_date, $period->end_date);
                $kpiItems = $this->getKpiComponents($emp->id, $period->start_date, $period->end_date, $period->company_id);
                $componentItems = array_merge($componentItems, $kpiItems);
                
                // Calculate Variable Components
                foreach ($componentItems as &$item) {
                     $item['quantity'] = 1; // Default
                     
                     if ($item['calculation_method'] === 'attendance_based') {
                         // Rate * Days
                         $item['quantity'] = $daysPresent;
                         $item['amount'] = $item['amount'] * $daysPresent; 
                         $item['label'] .= " ({$daysPresent} hari)";
                     } elseif ($item['calculation_method'] === 'formula' && !empty($item['formula'])) {
                         // Parse Formula
                         // Variables: basic_salary, days, hours, amount (the set amount)
                         // Formula example: "amount * hours" or "basic_salary * 0.1"
                         $vars = [
                             'basic_salary' => $emp->basic_salary,
                             'days' => $daysPresent,
                             'hours' => $totalWorkedHours,
                             'amount' => $item['amount'],
                         ];
                         
                         $item['amount'] = $this->evaluateComponentFormula($item['formula'], $vars);
                         
                         // Try to guess quantity for display
                         if (str_contains($item['formula'], 'days')) $item['quantity'] = $daysPresent;
                         if (str_contains($item['formula'], 'hours')) $item['quantity'] = $totalWorkedHours;
                     }
                }
                unset($item); // Break reference

                foreach ($componentItems as $item) {
                    if ($item['type'] === 'earning') {
                        $gross += $item['amount'];
                    }
                }

                $bpjsDetails = $this->calculateBpjsContributions($emp->basic_salary, $bpjsRates);
                [$loanAmount, $loanScheduleIds] = $this->getLoanInstallment($emp->id, $period->id);

                $monthlyTaxableBase = $gross - $bpjsDetails['employee_total'];
                $taxAmount = $this->calculatePph21($monthlyTaxableBase, $taxRates, $this->getEmployeePtkp($emp->id));

                $totalDeduction = $bpjsDetails['employee_total'] + $loanAmount + $taxAmount;
                foreach ($componentItems as $item) {
                    if ($item['type'] === 'deduction') {
                        $totalDeduction += $item['amount'];
                    }
                }

                $net = $gross - $totalDeduction;

                DB::table('payroll_headers')->updateOrInsert(
                    ['payroll_period_id' => $period->id, 'employee_id' => $emp->id],
                    [
                        'gross_income' => $gross,
                        'total_deduction' => $totalDeduction,
                        'net_income' => $net,
                        'status' => 'calculated',
                        'generated_at' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $header = DB::table('payroll_headers')
                     ->where('payroll_period_id', $period->id)
                     ->where('employee_id', $emp->id)
                     ->first();

                $components = $this->ensureComponents($period->company_id);

                DB::table('payroll_details')->where('payroll_header_id', $header->id)->delete();

                $detailRows = [
                    [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['BASIC_SALARY'],
                        'amount' => $emp->basic_salary,
                        'quantity' => 1,
                        'remark' => 'Gaji pokok',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['OVERTIME'],
                        'amount' => $overtimePay,
                        'quantity' => $overtimeMinutes / 60,
                        'remark' => 'Lembur',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];

                foreach ($componentItems as $item) {
                    $detailRows[] = [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $item['payroll_component_id'],
                        'amount' => $item['type'] === 'deduction' ? -1 * $item['amount'] : $item['amount'],
                        'quantity' => $item['quantity'] ?? 1,
                        'remark' => $item['label'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach ($bpjsDetails['employee_lines'] as $line) {
                    $detailRows[] = [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components[$line['code']],
                        'amount' => $line['amount'],
                        'quantity' => 1,
                        'remark' => $line['label'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach ($bpjsDetails['employer_lines'] as $line) {
                    // Porsi perusahaan dicatat untuk laporan, tidak mempengaruhi net
                    if (!isset($components[$line['code']])) {
                        continue;
                    }
                    $detailRows[] = [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components[$line['code']],
                        'amount' => $line['amount'],
                        'quantity' => 1,
                        'remark' => $line['label'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($loanAmount > 0 && isset($components['LOAN_INSTALLMENT'])) {
                    $detailRows[] = [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['LOAN_INSTALLMENT'],
                        'amount' => -1 * $loanAmount,
                        'quantity' => 1,
                        'remark' => 'Potongan angsuran pinjaman',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($taxAmount > 0 && isset($components['TAX_PPH21'])) {
                    $detailRows[] = [
                        'payroll_header_id' => $header->id,
                        'payroll_component_id' => $components['TAX_PPH21'],
                        'amount' => -1 * $taxAmount,
                        'quantity' => 1,
                        'remark' => 'PPh21 bulanan',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Bersihkan detail yang tidak punya komponen (jika mapping belum ada)
                $detailRows = array_values(array_filter($detailRows, fn($row) => !empty($row['payroll_component_id'])));

                DB::table('payroll_details')->insert($detailRows);

                // Tandai cicilan pinjaman periode ini sebagai dibayar
                if ($loanAmount > 0 && !empty($loanScheduleIds)) {
                    DB::table('employee_loan_schedules')
                        ->whereIn('id', $loanScheduleIds)
                        ->update(['is_paid' => true, 'paid_at' => now(), 'updated_at' => now()]);
                }

                // Kunci attendance yang masuk ke payroll periode ini
                DB::table('attendance_summaries')
                    ->where('employee_id', $emp->id)
                    ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                    ->update(['locked' => true, 'updated_at' => now()]);
            });
        }
    }

    /**
     * Simulasi payroll reguler tanpa menulis ke database.
     */
    public function simulateForPeriod(int $payrollPeriodId): array
    {
        $period = DB::table('payroll_periods')->find($payrollPeriodId);
        if (!$period) {
            throw new RuntimeException('Payroll period not found');
        }

        $bpjsRates = $this->getActiveBpjsRates($period->company_id, $period->end_date);
        $taxRates = $this->getActiveTaxRates($period->company_id, $period->end_date);

        $employees = DB::table('employees')
            ->where('company_id', $period->company_id)
            ->where('is_volunteer', false)
            ->where('status', 'active')
            ->get();

        $results = [];

        foreach ($employees as $emp) {
            $overtimeMinutes = DB::table('overtime_requests')
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->whereBetween('work_date', [$period->start_date, $period->end_date])
                ->sum('total_minutes');

            $hourlyRate = ($emp->basic_salary / 173);
            $overtimePay = $this->calculateOvertimePay($overtimeMinutes, $hourlyRate);
            $gross = (float) $emp->basic_salary + $overtimePay;
            $componentItems = $this->getEmployeeComponents($emp->id, $period->start_date, $period->end_date);
            $kpiItems = $this->getKpiComponents($emp->id, $period->start_date, $period->end_date, $period->company_id);
            $componentItems = array_merge($componentItems, $kpiItems);
            foreach ($componentItems as $item) {
                if ($item['type'] === 'earning') {
                    $gross += $item['amount'];
                }
            }
            $bpjsDetails = $this->calculateBpjsContributions($emp->basic_salary, $bpjsRates);
            [$loanAmount] = $this->getLoanInstallment($emp->id, $period->id);
            $taxAmount = $this->calculatePph21($gross - $bpjsDetails['employee_total'], $taxRates, $this->getEmployeePtkp($emp->id));

            $totalDeduction = $bpjsDetails['employee_total'] + $loanAmount + $taxAmount;
            foreach ($componentItems as $item) {
                if ($item['type'] === 'deduction') {
                    $totalDeduction += $item['amount'];
                }
            }
            $net = $gross - $totalDeduction;

            $components = [
                ['code' => 'BASIC_SALARY', 'label' => 'Gaji Pokok', 'amount' => $emp->basic_salary, 'quantity' => 1],
                ['code' => 'OVERTIME', 'label' => 'Lembur', 'amount' => $overtimePay, 'quantity' => $overtimeMinutes / 60],
            ];

            foreach ($componentItems as $item) {
                $components[] = [
                    'code' => $item['code'],
                    'label' => $item['label'],
                    'amount' => $item['type'] === 'deduction' ? -1 * $item['amount'] : $item['amount'],
                    'quantity' => 1,
                ];
            }

            foreach ($bpjsDetails['employee_lines'] as $line) {
                $components[] = ['code' => $line['code'], 'label' => $line['label'], 'amount' => $line['amount'], 'quantity' => 1];
            }
            foreach ($bpjsDetails['employer_lines'] as $line) {
                $components[] = ['code' => $line['code'], 'label' => $line['label'], 'amount' => $line['amount'], 'quantity' => 1];
            }
            if ($loanAmount > 0) {
                $components[] = ['code' => 'LOAN_INSTALLMENT', 'label' => 'Potongan pinjaman', 'amount' => -1 * $loanAmount, 'quantity' => 1];
            }
            if ($taxAmount > 0) {
                $components[] = ['code' => 'TAX_PPH21', 'label' => 'PPh21', 'amount' => -1 * $taxAmount, 'quantity' => 1];
            }

            $results[] = [
                'employee' => $emp,
                'gross' => $gross,
                'deduction' => $totalDeduction,
                'net' => $net,
                'components' => $components,
            ];
        }

        return $results;
    }

    private function calculateOvertimePay(int $minutes, float $hourlyRate): float
    {
        if ($minutes <= 0) {
            return 0;
        }
        $hours = $minutes / 60;
        $firstHour = min(1, $hours);
        $remaining = max(0, $hours - 1);
        return ($firstHour * 1.5 * $hourlyRate) + ($remaining * 2 * $hourlyRate);
    }

    private function getActiveBpjsRates(int $companyId, string $asOfDate): array
    {
        $rows = DB::table('bpjs_rates')
            ->where('company_id', $companyId)
            ->where('effective_from', '<=', $asOfDate)
            ->where(function ($q) use ($asOfDate) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $asOfDate);
            })
            ->get()
            ->keyBy('program');

        return $rows->toArray();
    }

    private function calculateBpjsContributions(float $salary, array $bpjsRates): array
    {
        $employeeLines = [];
        $employerLines = [];
        $employeeTotal = 0.0;
        $employerTotal = 0.0;

        foreach ($bpjsRates as $program => $rate) {
            $base = $salary;
            if ($rate->salary_cap_min) {
                $base = max($base, (float) $rate->salary_cap_min);
            }
            if ($rate->salary_cap_max) {
                $base = min($base, (float) $rate->salary_cap_max);
            }

            $empAmt = $base * ((float) $rate->employee_rate / 100);
            $erAmt = $base * ((float) $rate->employer_rate / 100);
            if ($empAmt <= 0) {
                $empAmt = 0;
            }

            $code = match ($program) {
                'bpjs_kesehatan' => 'BPJS_KES_EMP',
                'jht' => 'BPJS_JHT_EMP',
                'jkk' => 'BPJS_JKK_EMP',
                'jkm' => 'BPJS_JKM_EMP',
                'jp' => 'BPJS_JP_EMP',
                default => 'BPJS_EMP',
            };

            $employeeLines[] = [
                'code' => $code,
                'amount' => -1 * $empAmt,
                'label' => strtoupper($program) . ' (karyawan)',
            ];

            $employeeTotal += $empAmt;

            if ($erAmt > 0) {
                $erCode = match ($program) {
                    'bpjs_kesehatan' => 'BPJS_KES_ER',
                    'jht' => 'BPJS_JHT_ER',
                    'jkk' => 'BPJS_JKK_ER',
                    'jkm' => 'BPJS_JKM_ER',
                    'jp' => 'BPJS_JP_ER',
                    default => 'BPJS_ER',
                };
                $employerLines[] = [
                    'code' => $erCode,
                    'amount' => $erAmt,
                    'label' => strtoupper($program) . ' (perusahaan)',
                ];
                $employerTotal += $erAmt;
            }
        }

        return [
            'employee_total' => $employeeTotal,
            'employer_total' => $employerTotal,
            'employee_lines' => $employeeLines,
            'employer_lines' => $employerLines,
        ];
    }

    private function ensureComponents(int $companyId): array
    {
        $now = now();
        $definitions = [
            'BASIC_SALARY' => ['name' => 'Basic Salary', 'type' => 'earning', 'category' => 'fixed', 'method' => 'manual', 'seq' => 5],
            'OVERTIME' => ['name' => 'Overtime', 'type' => 'earning', 'category' => 'variable', 'method' => 'attendance_based', 'seq' => 10],
            'BPJS_KES_EMP' => ['name' => 'BPJS Kesehatan (Karyawan)', 'type' => 'deduction', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 50],
            'BPJS_JHT_EMP' => ['name' => 'BPJS JHT (Karyawan)', 'type' => 'deduction', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 51],
            'BPJS_JKK_EMP' => ['name' => 'BPJS JKK (Karyawan)', 'type' => 'deduction', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 52],
            'BPJS_JKM_EMP' => ['name' => 'BPJS JKM (Karyawan)', 'type' => 'deduction', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 53],
            'BPJS_JP_EMP' => ['name' => 'BPJS JP (Karyawan)', 'type' => 'deduction', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 54],
            'BPJS_KES_ER' => ['name' => 'BPJS Kesehatan (Perusahaan)', 'type' => 'earning', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 55],
            'BPJS_JHT_ER' => ['name' => 'BPJS JHT (Perusahaan)', 'type' => 'earning', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 56],
            'BPJS_JKK_ER' => ['name' => 'BPJS JKK (Perusahaan)', 'type' => 'earning', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 57],
            'BPJS_JKM_ER' => ['name' => 'BPJS JKM (Perusahaan)', 'type' => 'earning', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 58],
            'BPJS_JP_ER' => ['name' => 'BPJS JP (Perusahaan)', 'type' => 'earning', 'category' => 'bpjs', 'method' => 'manual', 'seq' => 59],
            'LOAN_INSTALLMENT' => ['name' => 'Loan Installment', 'type' => 'deduction', 'category' => 'loan', 'method' => 'manual', 'seq' => 70],
            'TAX_PPH21' => ['name' => 'PPh21', 'type' => 'deduction', 'category' => 'tax', 'method' => 'manual', 'seq' => 80],
        ];

        foreach ($definitions as $code => $def) {
            DB::table('payroll_components')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $code],
                [
                    'name' => $def['name'],
                    'type' => $def['type'],
                    'category' => $def['category'],
                    'calculation_method' => $def['method'],
                    'is_taxable' => $def['type'] === 'earning',
                    'show_in_payslip' => true,
                    'sequence' => $def['seq'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return DB::table('payroll_components')
            ->where('company_id', $companyId)
            ->whereIn('code', array_keys($definitions))
            ->pluck('id', 'code')
            ->toArray();
    }

    private function getActiveTaxRates(int $companyId, string $asOfDate): array
    {
        $year = (int) Carbon::parse($asOfDate)->year;
        return DB::table('tax_rates')
            ->where('company_id', $companyId)
            ->where('year', $year)
            ->orderBy('range_min')
            ->get()
            ->toArray();
    }

    private function calculatePph21(float $monthlyGrossAfterBpjs, array $taxRates, string $ptkpStatus): float
    {
        if ($monthlyGrossAfterBpjs <= 0 || empty($taxRates)) {
            return 0.0;
        }

        $ptkpAnnual = $this->getPtkpAmount($ptkpStatus);
        $jobExpense = min($monthlyGrossAfterBpjs * 0.05, 500000); // batas biaya jabatan bulanan

        $annualTaxable = max(0, ($monthlyGrossAfterBpjs - $jobExpense) * 12 - $ptkpAnnual);
        $annualTax = 0.0;
        $remaining = $annualTaxable;

        foreach ($taxRates as $rate) {
            $min = (float) $rate->range_min;
            $max = $rate->range_max ? (float) $rate->range_max : INF;
            if ($annualTaxable <= $min) {
                continue;
            }
            $chargeable = min($remaining, $max - $min);
            if ($chargeable <= 0) {
                continue;
            }
            $annualTax += $chargeable * ((float) $rate->rate_percent / 100);
            $remaining -= $chargeable;
            if ($remaining <= 0) {
                break;
            }
        }

        return $annualTax / 12;
    }

    private function getEmployeeComponents(int $employeeId, string $startDate, string $endDate): array
    {
        return DB::table('employee_payroll_components as epc')
            ->join('payroll_components as pc', 'pc.id', '=', 'epc.payroll_component_id')
            ->where('epc.employee_id', $employeeId)
            ->where('pc.show_in_payslip', true)
            ->where('epc.effective_from', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->whereNull('epc.effective_to')->orWhere('epc.effective_to', '>=', $startDate);
            })
            ->orderBy('pc.sequence')
            ->get()
            ->map(function ($row) {
                return [
                    'payroll_component_id' => $row->payroll_component_id,
                    'code' => $row->code,
                    'label' => $row->name,
                    'type' => $row->type,
                    'amount' => (float) $row->amount,
                    'calculation_method' => $row->calculation_method, // Added
                ];
            })
            ->all();
    }

    private function getKpiComponents(int $employeeId, string $startDate, string $endDate, int $companyId): array
    {
        $rows = DB::table('employee_kpi_results as r')
            ->join('kpi_payroll_mapping as m', 'm.kpi_id', '=', 'r.kpi_id')
            ->join('payroll_components as pc', 'pc.id', '=', 'm.payroll_component_id')
            ->where('r.employee_id', $employeeId)
            ->whereBetween('r.period_start', [$startDate, $endDate])
            ->whereBetween('r.period_end', [$startDate, $endDate])
            ->where('pc.company_id', $companyId)
            ->select('r.actual_value', 'r.achievement_percentage', 'm.max_amount', 'm.formula', 'pc.id as payroll_component_id', 'pc.code', 'pc.name', 'pc.type')
            ->get();

        return $rows->map(function ($row) {
            $amount = $this->evaluateKpiAmount($row->formula, (float) $row->actual_value, (float) $row->achievement_percentage);
            if ($row->max_amount && $amount > (float) $row->max_amount) {
                $amount = (float) $row->max_amount;
            }
            return [
                'payroll_component_id' => $row->payroll_component_id,
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
            // Suppress errors; simple eval for arithmetic only
            $result = eval("return {$expr};");
            return is_numeric($result) ? (float) $result : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function getLoanInstallment(int $employeeId, int $payrollPeriodId): array
    {
        $schedules = DB::table('employee_loan_schedules as els')
            ->join('employee_loans as el', 'el.id', '=', 'els.employee_loan_id')
            ->where('el.employee_id', $employeeId)
            ->where('els.payroll_period_id', $payrollPeriodId)
            ->where('els.is_paid', false)
            ->get(['els.id', 'els.amount']);

        $total = $schedules->sum('amount');
        $ids = $schedules->pluck('id')->all();

        return [$total, $ids];
    }

    private function getEmployeePtkp(int $employeeId): string
    {
        $profile = DB::table('employee_tax_profiles')->where('employee_id', $employeeId)->first();
        return $profile?->ptkp_status ?? 'TK/0';
    }

    private function getPtkpAmount(string $ptkpStatus): float
    {
        $map = [
            'TK/0' => 54000000,
            'TK/1' => 58500000,
            'TK/2' => 63000000,
            'TK/3' => 67500000,
            'K/0' => 58500000,
            'K/1' => 63000000,
            'K/2' => 67500000,
            'K/3' => 72000000,
        ];
        return $map[$ptkpStatus] ?? 54000000;
    }

    private function evaluateComponentFormula(?string $formula, array $vars): float
    {
        if (!$formula) {
            return 0;
        }

        $cleanFormula = str_replace(array_keys($vars), '', $formula);
        if (preg_match('/[a-z]/i', $cleanFormula)) {
             return 0; 
        }

        $expr = $formula;
        foreach ($vars as $key => $val) {
             $expr = str_replace($key, $val, $expr);
        }

        try {
            $result = eval("return {$expr};");
            return is_numeric($result) ? (float) $result : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
