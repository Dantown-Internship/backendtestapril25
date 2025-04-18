<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_an_expense()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'title' => 'Test Expense',
            'amount' => 100,
            'category' => 'Office Supplies',
        ];

        $response = $this->postJson('/api/expenses', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Expense stored successfully',
                'data' => [
                    'title' => 'Test Expense',
                    'amount' => 100,
                    'category' => 'Office Supplies'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_an_expense()
    {
        $user = User::factory()->create();
     
        $this->actingAs($user);
    
        $expense = Expense::factory()->create([
            'title' => 'Old Expense',
            'amount' => 50,
            'category' => 'Food',
            'company_id' => $user->company_id,
            'user_id' => $user->id,
        ]);
    
        $data = [
            'title' => 'Updated Expense',
            'amount' => 75,
            'category' => 'Food',
        ];
    
        $response = $this->putJson("/api/expenses/{$expense->id}", $data);
    
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Expense updated successfully',
                     'data' => [
                         'id' => $expense->id,
                         'title' => 'Updated Expense',
                         'amount' => 75,
                     ]
                 ]);
    
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated Expense',
            'amount' => 75,
        ]);
    }

    /** @test */
    public function it_can_delete_an_expense()
    {
        $user = User::factory()->create();

        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
    
        $this->actingAs($user);
    
        $response = $this->deleteJson("/api/expenses/{$expense->id}", []);
    
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Expense deleted successfully',
                 ]);
    }
}
