<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Models\ExpenseClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_claim_flow()
    {
        // 1. Setup
        $company = Company::create(['name' => 'Test Company', 'code' => 'TC']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'employee_code' => 'EMP001',
            'full_name' => 'John Doe',
            'is_volunteer' => true,
            'status' => 'active',
        ]);

        // 2. Submit Claim
        $response = $this->actingAs($user)->postJson('/api/expense-claims', [
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'date' => '2025-01-15',
            'amount' => 150000,
            'description' => 'Transport reimbursement',
        ]);

        $response->assertStatus(201);
        $claimId = $response->json('data.id');

        $this->assertDatabaseHas('expense_claims', [
            'id' => $claimId,
            'status' => 'pending',
            'amount' => 150000,
        ]);

        // 3. Approve Claim
        $response = $this->actingAs($user)->postJson("/api/expense-claims/{$claimId}/status", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expense_claims', [
            'id' => $claimId,
            'status' => 'approved',
            'approved_by' => $user->id,
        ]);

        // 4. List Claims
        $response = $this->actingAs($user)->getJson('/api/expense-claims');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
