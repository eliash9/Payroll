<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VolunteerPayrollComponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $companies = DB::table('companies')->get(['id']);

            if ($companies->isEmpty()) {
                // Seed a default company to attach components if none exist yet.
                $companyId = DB::table('companies')->insertGetId([
                    'name' => 'Default Company',
                    'code' => 'DEFAULT',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $companies = collect([['id' => $companyId]]);
            }

            foreach ($companies as $company) {
                $now = now();
                $components = [
                    [
                        'company_id' => $company->id,
                        'name' => 'Hourly Income',
                        'code' => 'HOURLY_INCOME',
                        'type' => 'earning',
                        'category' => 'variable',
                        'calculation_method' => 'attendance_based',
                        'is_taxable' => false,
                        'show_in_payslip' => true,
                        'sequence' => 10,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'company_id' => $company->id,
                        'name' => 'Fundraising Commission',
                        'code' => 'FUNDRAISING_COMMISSION',
                        'type' => 'earning',
                        'category' => 'kpi',
                        'calculation_method' => 'kpi_based',
                        'is_taxable' => false,
                        'show_in_payslip' => true,
                        'sequence' => 20,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'company_id' => $company->id,
                        'name' => 'Target Bonus',
                        'code' => 'TARGET_BONUS',
                        'type' => 'earning',
                        'category' => 'kpi',
                        'calculation_method' => 'kpi_based',
                        'is_taxable' => false,
                        'show_in_payslip' => true,
                        'sequence' => 30,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ];

                foreach ($components as $component) {
                    DB::table('payroll_components')->updateOrInsert(
                        ['code' => $component['code'], 'company_id' => $component['company_id']],
                        $component
                    );
                }
            }
    }
}
