<?php

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    // Create two companies for isolation tests
    $this->companyA = Company::factory()->create();
    $this->companyB = Company::factory()->create();

    // Create users with different roles
    $this->adminA = User::factory()->create(['company_id' => $this->companyA->id, 'role' => 'Admin']);
    $this->managerA = User::factory()->create(['company_id' => $this->companyA->id, 'role' => 'Manager']);
    $this->employeeA = User::factory()->create(['company_id' => $this->companyA->id, 'role' => 'Employee']);
    $this->adminB = User::factory()->create(['company_id' => $this->companyB->id, 'role' => 'Admin']);
});

it('allows an admin to list their company expenses', function () {
    Expense::factory()->count(3)->create(['company_id' => $this->companyA->id]);
    Expense::factory()->count(2)->create(['company_id' => $this->companyB->id]);

    Sanctum::actingAs($this->adminA);
    getJson('/api/expenses')
        ->assertOk()
        ->assertJsonCount(3, 'data.data');
});

it('allows an employee to create an expense', function () {
    Sanctum::actingAs($this->employeeA);

    postJson('/api/expenses', [
        'title' => 'Office Supplies',
        'amount' => 50.00,
        'category' => 'office'
    ])->assertCreated()
        ->assertJsonFragment(['title' => 'Office Supplies']);

    expect(Expense::where('user_id', $this->employeeA->id)->count())->toBe(1);
});

it('prevents an employee from deleting an expense', function () {
    $expense = Expense::factory()->create(['company_id' => $this->companyA->id]);

    Sanctum::actingAs($this->employeeA);
    deleteJson("/api/expenses/{$expense->id}")
        ->assertForbidden();
});

it('allows a manager to update an expense', function () {
    $expense = Expense::factory()->create(['company_id' => $this->companyA->id, 'title' => 'Old']);

    Sanctum::actingAs($this->managerA);
    putJson("/api/expenses/{$expense->id}", ['title' => 'Updated'])
        ->assertOk()
        ->assertJsonFragment(['title' => 'Updated']);
});

it('prevents a manager from deleting an expense', function () {
    $expense = Expense::factory()->create(['company_id' => $this->companyA->id]);

    Sanctum::actingAs($this->managerA);
    deleteJson("/api/expenses/{$expense->id}")
        ->assertForbidden();
});

it('prevents access to another company\'s expenses', function () {
    $expenseA = Expense::factory()->create(['company_id' => $this->companyA->id]);

    Sanctum::actingAs($this->adminB);
    getJson("/api/expenses?search={$expenseA->title}")
        ->assertOk()
        ->assertJsonCount(0, 'data.data');
});
