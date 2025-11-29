<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegularPayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_payroll_flow()
    {
        // 1. Setup Company and User
        $company = Company::create([
            'name' => 'Test Company',
            'code' => 'TC001',
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        // 2. Create Regular Employee
        $employee = Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'REG001',
            'full_name' => 'Regular Employee',
            'is_volunteer' => false,
            'basic_salary' => 5000000, // 5 Million
            'status' => 'active',
            'join_date' => now(),
        ]);

        // 3. Create Payroll Period
        $startDate = Carbon::parse('2025-01-01');
        $endDate = Carbon::parse('2025-01-31');
        
        $period = PayrollPeriod::create([
            'company_id' => $company->id,
            'code' => '2025-01',
            'name' => 'January 2025',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // 4. Create Overtime Request (Approved)
        // 2 hours overtime
        DB::table('overtime_requests')->insert([
            'employee_id' => $employee->id,
            'work_date' => $startDate->copy()->addDays(5),
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'total_minutes' => 120,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Run Payroll Generation via API
        $response = $this->actingAs($user)
            ->postJson("/api/payroll-periods/{$period->id}/generate-regular-payroll");

        $response->assertStatus(200);

        // 6. Verify Payroll Header
        $this->assertDatabaseHas('payroll_headers', [
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
        ]);

        $header = DB::table('payroll_headers')
            ->where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->first();

        // Expected Calculation:
        // Basic Salary: 5,000,000
        // Overtime: 2 hours. 
        // Hourly Rate = 5,000,000 / 173 = 28,901.73
        // Overtime Pay:
        // 1st hour: 1.5 * 28,901.73 = 43,352.60
        // 2nd hour: 2.0 * 28,901.73 = 57,803.46
        // Total Overtime: ~101,156
        // Gross: 5,101,156

        $this->assertGreaterThan(5000000, $header->gross_income);
        
        // 7. Verify Payroll Details
        // Check for Basic Salary
        $this->assertDatabaseHas('payroll_details', [
            'payroll_header_id' => $header->id,
            'amount' => 5000000,
        ]);

        // Check for Overtime
        // We check if a detail with amount > 0 exists for overtime component (we don't know the ID yet, but we can query by remark or check existence)
        $overtimeDetail = DB::table('payroll_details')
            ->where('payroll_header_id', $header->id)
            ->where('remark', 'Lembur')
            ->first();
            
        $this->assertNotNull($overtimeDetail);
        $this->assertTrue($overtimeDetail->amount > 100000); // Should be around 101k
    }
}
