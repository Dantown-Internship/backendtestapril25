<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class EmployeeExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->employee = User::factory()->create([
            'role' => 'Employee',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->employee);
    }

    public function test_employee_can_create_expense()
    {
        $payload = [
            'title' => 'Taxi Ride',
            'amount' => 2000.00,
            'category' => 'Travel'
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Taxi Ride']);
    }

    public function test_employee_can_list_expenses()
    {
        Expense::factory()->count(3)->create([
            'company_id' => $this->employee->company_id,
            'user_id' => $this->employee->id
        ]);

        $response = $this->getJson('/api/expenses');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_employee_cannot_update_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->employee->company_id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Changed',
            'amount' => 2000,
            'category' => 'Other'
        ]);

        $response->assertStatus(403);
    }

    public function test_employee_cannot_delete_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->employee->company_id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
    }
}