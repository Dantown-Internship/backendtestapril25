<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_only_returns_expenses_related_to_user_company(): void
    {
        $countOfValidExpenses = 5;
        $user = User::factory()->create();
        $expensesForOtherCompanies = Expense::factory(10)->create();
        $expensesForUserCompany = Expense::factory($countOfValidExpenses)->for($user, "owner")->for($user->company, "company")->create();

        $response = $this->actingAs($user)->getJson(route("expenses.index"));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($countOfValidExpenses, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'category',
                        'amount',
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'url',
                            'label',
                            'active',
                        ],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_it_can_search_for_title_or_description_in_expenses(): void
    {
        $user = User::factory()->create();
        $expenseWithSearchTermOfTitle = Expense::factory(1)->create(['title' => 'title', 'company_id' => $user->company_id, 'user_id' => $user->id]);
        $expenseWithSearchTermOfCategory = Expense::factory(2)->create(['category' => 'category', 'company_id' => $user->company_id, 'user_id' => $user->id]);
        $expenseWithSearchTermOfHidden = Expense::factory(4)->for($user, "owner")->for($user->company, "company")->create(['title' => 'show']);



        $response = $this->actingAs($user)->getJson(route("expenses.index", ['search' => 'title']));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'category',
                        'amount',
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'url',
                            'label',
                            'active',
                        ],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $response = $this->actingAs($user)->getJson(route("expenses.index", ['search' => 'category']));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');

        $response = $this->actingAs($user)->getJson(route("expenses.index", ['search' => 'show']));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(4, 'data');
    }

    public function test_it_can_create_expense_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Lunch with client',
            'category' => 'Food',
            'amount' => 45.50,
        ];

        $response = $this->actingAs($user)->postJson(route("expenses.store"), $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'category',
                    'amount'
                ]
            ]);
    }

    public function test_it_allows_only_admins_or_managers_to_update_expenses(): void
    {

        $adminOrManager = User::factory()->create([
            'role' => fake()->randomElement([RoleEnum::ADMIN(), RoleEnum::MANAGER()]),
        ]);
        $expense = Expense::factory()->for($adminOrManager, "owner")->for($adminOrManager->company, "company")->create();

        $payload = [
            'title' => 'Updated Title',
            'category' => 'Updated Category',
            'amount' => 0.00,
        ];

        $response = $this->actingAs($adminOrManager)->patchJson(route("expenses.update", $expense), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'category',
                    'amount'
                ]
            ]);

        $this->assertDatabaseHas(app(Expense::class)->getTable(), $payload);

        $adminOrManager->update(["role" => RoleEnum::EMPLOYEE()]);
        $employee = $adminOrManager;

        $response = $this->actingAs($employee)->patchJson(route("expenses.update", $expense), $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_it_allows_only_admins_to_delete_expenses(): void
    {

        $admin = User::factory()->create(['role' => RoleEnum::ADMIN()]);
        $expenses = Expense::factory(2)->for($admin, "owner")->for($admin->company, "company")->create();


        $response = $this->actingAs($admin)->deleteJson(route("expenses.destroy", $expenses[0]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing(app(Expense::class)->getTable(), $expenses[0]->toArray());

        $admin->update([
            'role' => fake()->randomElement([
                RoleEnum::EMPLOYEE(),
                RoleEnum::MANAGER(),
            ]),
        ]);

        $employeeOrManager = $admin;

        $response = $this->actingAs($employeeOrManager)->deleteJson(route("expenses.destroy", $expenses[1]));

        $this->assertDatabaseHas(app(Expense::class)->getTable(), $expenses[1]->toArray());

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
