<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->manager = User::factory()->create(['role' => 'Manager', 'company_id' => $this->admin->company_id]);
        $this->employee = User::factory()->create(['role' => 'Employee', 'company_id' => $this->admin->company_id]);
        
        $this->adminToken = $this->admin->createToken('API Token')->plainTextToken;
        $this->managerToken = $this->manager->createToken('API Token')->plainTextToken;
        $this->employeeToken = $this->employee->createToken('API Token')->plainTextToken;
    }

    public function test_employee_can_create_expense(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->employeeToken,
        ])->postJson('/api/expenses', [
            'title' => 'Test Expense',
            'amount' => 100.50,
            'category' => 'Travel',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Expense',
                'amount' => 100.50,
                'category' => 'Travel',
            ]);
    }

    public function test_manager_can_update_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->managerToken,
        ])->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Expense',
            'amount' => 150.75,
            'category' => 'Food',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Expense',
                'amount' => 150.75,
                'category' => 'Food',
            ]);
    }

    public function test_admin_can_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}