<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ExpenseSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();

    // Create two companies
    $this->company = \App\Models\Company::factory()->create();
    $this->otherCompany = \App\Models\Company::factory()->create();

    // Users for main test company
    $this->admin = User::factory()->create([
        'role' => 'Admin',
        'company_id' => $this->company->id,
    ]);

    $this->manager = User::factory()->create([
        'role' => 'Manager',
        'company_id' => $this->company->id,
    ]);

    $this->employee = User::factory()->create([
        'role' => 'Employee',
        'company_id' => $this->company->id,
    ]);

    // A user from a different company
    $this->otherCompanyUser = User::factory()->create([
        'role' => 'Admin',
        'company_id' => $this->otherCompany->id,
    ]);

    // Expenses for the main company
    Expense::factory()->create([
        'title' => 'Team Lunch',
        'category' => 'Food',
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
    ]);

    Expense::factory()->create([
        'title' => 'Client Travel',
        'category' => 'Transport',
        'company_id' => $this->company->id,
        'user_id' => $this->manager->id,
    ]);

    Expense::factory()->create([
        'title' => 'Software License',
        'category' => 'Technology',
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    // Expense from other company
    Expense::factory()->create([
        'title' => 'Outsider Expense',
        'category' => 'Misc',
        'company_id' => $this->otherCompany->id,
        'user_id' => $this->otherCompanyUser->id,
    ]);
}


    public function test_admin_can_search_expenses_by_title_or_category()
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->getJson('/api/expenses?search=Lunch');
        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Team Lunch'])
                 ->assertJsonMissing(['title' => 'Outsider Expense']);

        $response = $this->getJson('/api/expenses?search=Food');
        $response->assertOk()
                 ->assertJsonFragment(['category' => 'Food']);
    }

    public function test_manager_can_search_expenses_by_title_or_category()
    {
        $this->actingAs($this->manager, 'sanctum');

        $response = $this->getJson('/api/expenses?search=Travel');
        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Client Travel']);

        $response = $this->getJson('/api/expenses?search=Transport');
        $response->assertOk()
                 ->assertJsonFragment(['category' => 'Transport']);
    }

    public function test_employee_can_search_expenses_by_title_or_category()
    {
        $this->actingAs($this->employee, 'sanctum');

        $response = $this->getJson('/api/expenses?search=Software');
        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Software License']);

        $response = $this->getJson('/api/expenses?search=Technology');
        $response->assertOk()
                 ->assertJsonFragment(['category' => 'Technology']);
    }

    public function test_expenses_are_scoped_to_company()
    {
        $this->actingAs($this->employee, 'sanctum');

        $response = $this->getJson('/api/expenses?search=Outsider');
        $response->assertOk();
        $this->assertEmpty($response->json('data'));
    }
}
