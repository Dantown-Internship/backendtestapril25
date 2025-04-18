<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ManagerExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->manager = User::factory()->create([
            'role' => 'Manager',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->manager);
    }

    public function test_manager_can_create_expense()
    {
        $payload = [
            'title' => 'Client Dinner',
            'amount' => 1200.50,
            'category' => 'Meals'
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Client Dinner']);
    }

    public function test_manager_can_list_expenses()
    {
        Expense::factory()->count(3)->create([
            'company_id' => $this->manager->company_id,
            'user_id' => $this->manager->id
        ]);

        $response = $this->getJson('/api/expenses');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_manager_can_update_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->manager->company_id,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Expense',
            'amount' => 7500,
            'category' => 'Supplies'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Expense']);
    }

    public function test_manager_cannot_delete_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->manager->company_id,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403); // Forbidden
    }
}