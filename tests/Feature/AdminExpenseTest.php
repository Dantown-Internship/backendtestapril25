<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AdminExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->admin);
    }

    public function test_admin_can_create_expense()
    {
        $payload = [
            'title' => 'Lunch Meeting',
            'amount' => 5000.00,
            'category' => 'Meals'
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Lunch Meeting']);
    }

    public function test_admin_can_list_expenses()
    {
        Expense::factory()->count(3)->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->getJson('/api/expenses');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_admin_can_update_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Title',
            'amount' => 100,
            'category' => 'Travel'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_admin_can_delete_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(204);
    }
}
