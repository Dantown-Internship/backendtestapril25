<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_user_can_view_expenses()
    {
        Expense::factory()->count(3)->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->getJson('/api/expenses');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'expenses' => [
                             'data' => [
                                 '*' => [
                                     'id',
                                     'title',
                                     'amount',
                                     'category',
                                     'company_id',
                                     'user_id',
                                     'created_at',
                                     'updated_at',
                                 ],
                             ],
                         ],
                     ],
                 ]);
    }

    public function test_user_can_create_expense()
    {
        $expenseData = [
            'title' => 'Test Expense',
            'amount' => 100.00,
            'category' => 'Office Supplies',
        ];

        $response = $this->postJson('/api/expenses', $expenseData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title' => 'Test Expense',
                     'amount' => 100.00,
                     'category' => 'Office Supplies',
                ]);

        $this->assertDatabaseHas('expenses', [
            'title' => 'Test Expense',
            'amount' => 100.00,
            'category' => 'Office Supplies',
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_single_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $expense->id,
                     'title' => $expense->title,
                ]);
    }

    public function test_user_can_update_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Expense',
            'amount' => 150.00,
            'category' => 'Travel',
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title' => 'Updated Expense',
                     'amount' => 150.00,
                     'category' => 'Travel',
                ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated Expense',
            'amount' => 150.00,
            'category' => 'Travel',
        ]);
    }

    public function test_user_can_delete_expense()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Expense deleted successfully',
                ]);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_unauthorized_user_cannot_update_expense()
    {
        $otherUser = User::factory()->create();
        $expense = Expense::factory()->create([
            'company_id' => $otherUser->company_id,
            'user_id' => $otherUser->id,
        ]);

        $updatedData = [
            'title' => 'Unauthorized Update',
            'amount' => 200.00,
            'category' => 'Unauthorized',
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $updatedData);

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_expense()
    {
        $otherUser = User::factory()->create();
        $expense = Expense::factory()->create([
            'company_id' => $otherUser->company_id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
    }
}
