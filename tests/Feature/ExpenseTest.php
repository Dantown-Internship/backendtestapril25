<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Uncomment to see detailed errors during development
        // $this->withoutExceptionHandling();
    }

    public function test_employee_can_create_expense()
    {
        $company = \App\Models\Company::factory()->create();
        $user = User::factory()->create([
            'role' => 'Employee',
            'company_id' => $company->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/expenses', [
            'title' => 'Test Expense',
            'amount' => 100,
            'category' => 'Travel'
        ]);

        $response->assertStatus(201);
    }

    public function test_manager_can_update_expense()
    {
        $company = \App\Models\Company::factory()->create();
        $manager = User::factory()->create([
            'role' => 'Manager',
            'company_id' => $company->id
        ]);

        Sanctum::actingAs($manager);

        $expense = Expense::factory()->create([
            'company_id' => $company->id,
            'user_id' => $manager->id
        ]);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Title'
        ]);

        $response->assertOk();
    }

    public function test_user_cannot_access_other_company_data()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user2, ['*']);

        $expense = Expense::factory()->create([
            'company_id' => $user1->company_id,
            'user_id' => $user1->id
        ]);

        $response = $this->getJson("/api/expenses/{$expense->id}");
        $response->assertForbidden();
    }
}
