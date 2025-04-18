<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use App\Jobs\WeeklyExpensesReport;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_of_expenses_are_retrieved(): void
    {
        $user = User::factory()->create();
        Expense::factory()->count(3)
            ->for($user)->for($user->company)
            ->create();

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson(route('expenses.index', ['company_id' => $user->company->id]));

        $response->assertStatus(200);
    }

    public function test_an_expense_can_be_created_by_users(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('API Token')->plainTextToken;

        $expenseData = Expense::factory()->make()->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('expenses.store'), $expenseData);

        $response->assertStatus(201);
        $this->assertCount(1, Expense::all());
    }

    public function test_users_cannot_update_expense(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('API Token')->plainTextToken;

        $expenseData = Expense::factory()->make()->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('expenses.store'), $expenseData);

        $response->assertStatus(201);
        $this->assertCount(1, Expense::all());

        $expenseData = [
            'company_id' => $user->company->id,
            'title' => 'Test Expense 2',
            'amount' => 200.00,
            'category' => 'Test Category 2',
            'description' => 'Test Description 2',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson(route('expenses.update', $user->company->id), $expenseData);

        $response->assertStatus(403);
        $this->assertCount(1, Expense::all());
    }

    public function test_only_admins_can_delete_expense(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('expenses.delete', 10));

        $response->assertStatus(404);
        $this->assertCount(0, Expense::all());
    }
}
