<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Expense;

class ExpenseUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_expense()
    {
        // Create a user and an expense
        $user = User::factory()->create();
        $expense = Expense::factory()->create([
            'user_id' => $user->id, // Assuming expense belongs to user
        ]);

        // Data to update
        $data = [
            'title' => 'Updated Title',
            'amount' => 150.50,
            'category' => 'Updated Category',
        ];

        // Act as user and make a PUT request
        $response = $this->actingAs($user)->putJson(route('expenses.update', $expense->id), $data);

        // Assert response and DB
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Record Updated successfully.']);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated Title',
            'amount' => 150.50,
            'category' => 'Updated Category',
        ]);
    }
}

