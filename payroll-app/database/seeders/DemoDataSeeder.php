<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $companyId = DB::table('companies')->where('code', 'DEMO')->value('id');
        if (!$companyId) {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Demo Co',
                'code' => 'DEMO',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $branchId = DB::table('branches')->where('code', 'JKT')->value('id');
        if (!$branchId) {
            $branchId = DB::table('branches')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Jakarta',
                'code' => 'JKT',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $deptId = DB::table('departments')->where('code', 'FR')->value('id');
        if (!$deptId) {
            $deptId = DB::table('departments')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Fundraising',
                'code' => 'FR',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $posId = DB::table('positions')->where('name', 'Fundraiser')->value('id');
        if (!$posId) {
            $posId = DB::table('positions')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Fundraiser',
                'grade' => 'Staff',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Regular employees
        $empRegularIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $existing = DB::table('employees')->where('employee_code', "EMP00{$i}")->value('id');
            if ($existing) {
                $empRegularIds[] = $existing;
                continue;
            }
            $empRegularIds[] = DB::table('employees')->insertGetId([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'department_id' => $deptId,
                'position_id' => $posId,
                'employee_code' => "EMP00{$i}",
                'full_name' => "Karyawan Reguler {$i}",
                'basic_salary' => 8000000 + ($i * 1000000),
                'payroll_type' => 'monthly',
                'employment_type' => 'permanent',
                'status' => 'active',
                'join_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Volunteers
        $volunteers = [];
        for ($i = 1; $i <= 2; $i++) {
            $existing = DB::table('employees')->where('employee_code', "VOL00{$i}")->value('id');
            if ($existing) {
                $volunteers[] = $existing;
                continue;
            }
            $volunteers[] = DB::table('employees')->insertGetId([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'department_id' => $deptId,
                'position_id' => $posId,
                'employee_code' => "VOL00{$i}",
                'full_name' => "Relawan {$i}",
                'is_volunteer' => true,
                'hourly_rate' => 50000 + ($i * 10000),
                'commission_rate' => 3 + ($i * 1),
                'payroll_type' => 'hourly',
                'employment_type' => 'contract',
                'status' => 'active',
                'join_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Payroll components seed ensured by VolunteerPayrollComponentsSeeder

        // Payroll period current month
        $periodCode = now()->format('Y-m');
        $payrollPeriodId = DB::table('payroll_periods')->where('code', $periodCode)->value('id');
        if (!$payrollPeriodId) {
            $payrollPeriodId = DB::table('payroll_periods')->insertGetId([
                'company_id' => $companyId,
                'code' => $periodCode,
                'name' => now()->format('F Y'),
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
                'status' => 'draft',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Attendance summaries for volunteers
        foreach ($volunteers as $volId) {
            $workDate = now()->toDateString();
            DB::table('attendance_summaries')->updateOrInsert(
                ['employee_id' => $volId, 'work_date' => $workDate],
                [
                    'status' => 'present',
                    'worked_minutes' => 8 * 60,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Fundraising transactions for volunteers
        foreach ($volunteers as $idx => $volId) {
            DB::table('fundraising_transactions')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'fundraiser_id' => $volId,
                    'date_received' => now()->toDateTimeString(),
                    'amount' => 1500000 + ($idx * 500000),
                ],
                [
                    'currency' => 'IDR',
                    'source' => 'offline',
                    'campaign_name' => 'Ramadhan',
                    'category' => 'donation',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Overtime & leave example for regular employees
        $leaveTypeId = DB::table('leave_types')->where('code', 'CT_TAHUNAN')->value('id');
        if (!$leaveTypeId) {
            $leaveTypeId = DB::table('leave_types')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Cuti Tahunan',
                'code' => 'CT_TAHUNAN',
                'is_paid' => true,
                'is_annual_quota' => true,
                'default_quota_days' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('overtime_requests')->updateOrInsert(
            [
                'employee_id' => $empRegularIds[0],
                'work_date' => now()->toDateString(),
                'start_time' => now()->setTime(18, 0),
                'end_time' => now()->setTime(20, 0),
            ],
            [
                'total_minutes' => 120,
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('leave_requests')->updateOrInsert(
            [
                'employee_id' => $empRegularIds[1],
                'leave_type_id' => $leaveTypeId,
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ],
            [
                'total_days' => 1,
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // KPI sample
        $kpiId = DB::table('kpi_master')->where('code', 'TOTAL_DONATION_AMOUNT')->value('id');
        if (!$kpiId) {
            $kpiId = DB::table('kpi_master')->insertGetId([
                'company_id' => $companyId,
                'name' => 'TOTAL_DONATION_AMOUNT',
                'code' => 'TOTAL_DONATION_AMOUNT',
                'type' => 'numeric',
                'target_default' => 1000000,
                'weight_default' => 1,
                'period_type' => 'monthly',
                'category' => 'individual',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('employee_kpi_results')->updateOrInsert(
            [
                'employee_id' => $volunteers[0],
                'kpi_id' => $kpiId,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
            ],
            [
                'actual_value' => 1500000,
                'achievement_percentage' => 150,
                'score' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Default admin user for login
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'company_id' => $companyId,
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
