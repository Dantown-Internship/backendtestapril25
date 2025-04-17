<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_users_can_create_expense(): void
    {
        foreach (Roles::cases() as $role) {
            $user = User::factory()->create([
                'role' => $role->value,
            ]);
        }

        $this->actingAs($user);

        $payload = Expense::factory()->make()->toArray();

        $response = $this->postJson(route('store.expense'), $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('expenses', [
            'title' => $payload['title']
        ]);
        $response->dump();
    }

    public function test_users_can_view_expenses(): void
    {
        foreach (Roles::cases() as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'company_id' => Company::factory(),
            ]);

            $knownTitle = 'Monthly Software License';
            $knownCategory = 'Software';

            Expense::factory()->create([
                'title' => $knownTitle,
                'category' => $knownCategory,
                'company_id' => $user->company_id,
                'user_id' => $user->id,
            ]);

            // create some random data
            Expense::factory()->count(3)->create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
            ]);

            $this->actingAs($user);

            //test general list
            $response = $this->getJson(route('get.expenses'));
            $response->assertStatus(200);
            $response->dump();
            $response->assertJsonFragment(['category' => $knownCategory]);

            //test search by title
            $searchResponse = $this->getJson(route('get.expenses', [
                'search' => 'software license'
            ]));
            $searchResponse->assertStatus(200);
            $searchResponse->dump();
            $searchResponse->assertJsonFragment(['title' => $knownTitle]);

            //test filter by category
            $categoryResponse = $this->getJson(route('get.expenses', [
                'category' => $knownCategory
            ]));
            $categoryResponse->assertStatus(200);
            $categoryResponse->dump();

            $categoryResponse->assertJsonFragment(['category' => $knownCategory]);
        }
    }

    public function test_admin_an_manager_can_only_update_expense(): void
    {
        $rolesAllowed = ['Admin', 'Manager'];
        $rolesNotAllowed = ['Employee'];

        foreach (array_merge($rolesAllowed, $rolesNotAllowed) as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $expense = Expense::factory()->create([
                'company_id' => $user->company_id,
            ]);

            $this->actingAs($user);

            $payload = ['title' => 'Updated Expense Title'];

            $response = $this->putJson(route('update.expense', $expense), $payload);

            if (in_array($role, $rolesAllowed)) {
                $response->assertStatus(200);
                $this->assertEquals('Updated Expense Title', $expense->fresh()->title);
                $this->assertDatabaseHas('audit_logs', [
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'action' => 'update',
                ]);
                $response->dump();
            } else {
                $response->assertStatus(403);
            }
        }
    }

    public function test_admin_can_only_delete_expense(): void
    {
        $rolesAllowed = ['Admin'];
        $rolesNotAllowed = ['Manager', 'Employee'];

        foreach (array_merge($rolesAllowed, $rolesNotAllowed) as $role) {
            $user = User::factory()->create([
                'role' => $role
            ]);
        }

        $this->actingAs($user);

        $expense = Expense::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $response = $this->deleteJson(route('destroy.expense', $expense));

        if (in_array($role, $rolesAllowed)) {
            $response->assertStatus(204);
            $response->dump();
            $this->assertDatabaseMissing('expenses', $expense->toArray());
        } else {
            $response->assertForbidden();
        }
    }
}
