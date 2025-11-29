<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\CommissionRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VolunteerPayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_volunteer_payroll_flow()
    {
        // 1. Setup Company and User
        $company = Company::create([
            'name' => 'Test NGO',
            'code' => 'NGO001',
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        // 2. Seed Components
        $this->seed(\Database\Seeders\VolunteerPayrollComponentsSeeder::class);

        // 3. Create Volunteer
        $volunteer = Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'VOL001',
            'full_name' => 'Ahmad Volunteer',
            'is_volunteer' => true,
            'hourly_rate' => 50000,
            'commission_rate' => 5.0, // 5%
            'status' => 'active',
            'join_date' => now(),
        ]);

        // 4. Create Payroll Period
        $startDate = Carbon::parse('2025-01-01');
        $endDate = Carbon::parse('2025-01-31');
        
        $period = PayrollPeriod::create([
            'company_id' => $company->id,
            'code' => '2025-01',
            'name' => 'January 2025',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // 5. Simulate Attendance (10 hours)
        DB::table('attendance_summaries')->insert([
            'employee_id' => $volunteer->id,
            'work_date' => $startDate->copy()->addDays(1),
            'worked_minutes' => 600, // 10 hours
            'status' => 'present',
        ]);

        // 6. Simulate Fundraising Transactions (1,000,000 IDR)
        DB::table('fundraising_transactions')->insert([
            'company_id' => $company->id,
            'fundraiser_id' => $volunteer->id,
            'amount' => 1000000,
            'date_received' => $startDate->copy()->addDays(2),
            'source' => 'offline',
            'status' => 'verified',
        ]);

        // 7. Run Payroll Generation via API
        $response = $this->actingAs($user)
            ->postJson("/api/payroll-periods/{$period->id}/generate-volunteer-payroll");

        $response->assertStatus(200);

        // 8. Verify Payroll Header
        $this->assertDatabaseHas('payroll_headers', [
            'payroll_period_id' => $period->id,
            'employee_id' => $volunteer->id,
        ]);

        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $period->id)
            ->where('employee_id', $volunteer->id)
            ->first();

        // Expected Calculation:
        // Hourly: 10 hours * 50,000 = 500,000
        // Commission: 5% * 1,000,000 = 50,000
        // Total: 550,000

        $this->assertEquals(550000, $header->gross_income);
        $this->assertEquals(550000, $header->net_income);

        // 9. Verify Payroll Details
        $this->assertDatabaseHas('payroll_details', [
            'payroll_header_id' => $header->id,
            'amount' => 500000, // Hourly
        ]);

        $this->assertDatabaseHas('payroll_details', [
            'payroll_header_id' => $header->id,
            'amount' => 50000, // Commission
        ]);
    }

    public function test_volunteer_payroll_with_tiered_commission()
    {
        // 1. Setup Company and User
        $company = Company::create([
            'name' => 'Test NGO Tiered',
            'code' => 'NGO002',
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        // 2. Seed Components
        $this->seed(\Database\Seeders\VolunteerPayrollComponentsSeeder::class);

        // 3. Create Commission Rules
        // Tier 1: 0 - 10M => 2%
        CommissionRule::create([
            'company_id' => $company->id,
            'name' => 'Tier 1',
            'min_amount' => 0,
            'max_amount' => 10000000,
            'rate' => 2.0,
        ]);
        // Tier 2: 10M+ => 5%
        CommissionRule::create([
            'company_id' => $company->id,
            'name' => 'Tier 2',
            'min_amount' => 10000000,
            'max_amount' => null,
            'rate' => 5.0,
        ]);

        // 4. Create Volunteer
        $volunteer = Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'VOL002',
            'full_name' => 'Budi Tier',
            'is_volunteer' => true,
            'hourly_rate' => 0,
            'commission_rate' => 0, // Should be ignored
            'status' => 'active',
            'join_date' => now(),
        ]);

        // 5. Create Payroll Period
        $startDate = Carbon::parse('2025-02-01');
        $endDate = Carbon::parse('2025-02-28');
        
        $period = PayrollPeriod::create([
            'company_id' => $company->id,
            'code' => '2025-02',
            'name' => 'February 2025',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // 6. Simulate Fundraising Transactions (15,000,000 IDR) -> Should hit Tier 2 (5%)
        DB::table('fundraising_transactions')->insert([
            'company_id' => $company->id,
            'fundraiser_id' => $volunteer->id,
            'amount' => 15000000,
            'date_received' => $startDate->copy()->addDays(5),
            'source' => 'online',
            'status' => 'verified',
        ]);

        // 7. Run Payroll Generation via API
        $response = $this->actingAs($user)
            ->postJson("/api/payroll-periods/{$period->id}/generate-volunteer-payroll");

        $response->assertStatus(200);

        // 8. Verify Commission
        // Total Donation: 15,000,000
        // Expected Rate: 5% (Tier 2)
        // Commission: 750,000

        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $period->id)
            ->where('employee_id', $volunteer->id)
            ->first();

        $this->assertEquals(750000, $header->gross_income);

        $this->assertDatabaseHas('payroll_details', [
            'payroll_header_id' => $header->id,
            'amount' => 750000,
        ]);
    }
}
