<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles, permissions, etc. here if needed
    }

    public function test_admin_can_list_expenses()
    {
        $user = User::factory()->create(['role' => 'Admin']);
        $token = $user->createToken('TestToken')->plainTextToken; // Generate Sanctum token

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/expenses');
        $response->assertStatus(200);
    }

    public function test_employee_can_create_expense()
    {
        $user = User::factory()->create(['role' => 'Employee']); // Ensure it's a single instance
        $token = $user->createToken('TestToken')->plainTextToken; // Generate Sanctum token

        Sanctum::actingAs($user, ['*']);

        $payload = [
            'title' => 'Internet Bill',
            'amount' => 150.50,
            'category' => 'Utilities',
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expenses', [
            'title' => 'Internet Bill',
            'amount' => 150.50,
        ]);
    }

    public function test_manager_can_update_expense()
    {
        $user = User::factory()->create(['role' => 'Manager']); // Ensure it's a single instance
        // Ensure the expense is associated with the correct company
        $expense = Expense::factory()->create(['company_id' => $user->company_id]);

        $token = $user->createToken('TestToken')->plainTextToken; // Generate Sanctum token

        Sanctum::actingAs($user, ['*']);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Title',
            'amount' => 300,
            'category' => 'Misc',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', ['title' => 'Updated Title']);
    }

    public function test_non_authorized_user_cannot_delete_expense()
    {
        $user = User::factory()->create(['role' => 'Employee']); // Ensure it's a single instance
        $expense = Expense::factory()->create(['company_id' => $user->company_id]);

        $token = $user->createToken('TestToken')->plainTextToken; // Generate Sanctum token

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
    }
}