<?php

namespace Tests\Feature;





use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\WithFaker;

class ExpenseCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_expense()
    {
        // Create user
        $user = User::factory()->create([
            'company_id' => 1, // assuming your users have a company_id
        ]);

        // Act as the user
        $this->actingAs($user);

        // Prepare the request data
        $data = [
            'title' => 'Business Lunch',
            'amount' => 75.00,
            'category' => 'Food',
        ];

        // Send POST request
        $response = $this->postJson('/expenses', $data);

        // Check response
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Expense Created successfully.',
                     'data' => [
                         'Expense Detail' => [
                             'title' => 'Business Lunch',
                             'amount' => 75.00,
                             'category' => 'Food',
                         ],
                     ],
                 ]);

        // Check database
        $this->assertDatabaseHas('expenses', [
            'title' => 'Business Lunch',
            'amount' => 75.00,
            'category' => 'Food',
            'company_id' => $user->company_id,
            'user_id' => $user->id,
        ]);
    }
}
