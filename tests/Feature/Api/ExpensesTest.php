<?php

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->user = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Admin',
    ]);
    Sanctum::actingAs($this->user, ['*']);
});

test('fetches a list of expenses for the authenticated user\'s company', function () {
    Expense::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
    ]);

    $response = $this->getJson('/api/expenses');

    $response->assertOk();
    expect($response['data'])->toHaveCount(5);
});

test('authenticated user can\'t see other company\'s expenses', function () {
    $anotherCompany = Company::factory()->create();
    $AnotherUser = User::factory()->create([
        'company_id' => $anotherCompany->id,
        'role' => 'Admin',
    ]);

    $anotherCompanyExpenses = [
        [
            'user_id' => $AnotherUser->id,
            'company_id' => $anotherCompany->id,
            'title' => 'Office Supplies',
            'amount' => 2000,
            'category' => 'Office',
        ],
        [
            'user_id' => $AnotherUser->id,
            'company_id' => $anotherCompany->id,
            'title' => 'Travel Expenses',
            'amount' => 1500,
            'category' => 'Travel',
        ],

    ];

    Expense::factory()->createMany($anotherCompanyExpenses);

    Expense::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
    ]);

    $response = $this->getJson('/api/expenses');

    $response->assertOk();

    expect(Expense::all())->toHaveCount(5);
    expect($response['data'])->toHaveCount(3);
});

test('creates a new expense', function () {
    $response = $this->postJson('/api/expenses', [
        'title' => 'Fuel',
        'amount' => 5000,
        'category' => 'Transportation',
    ]);

    $response->assertCreated();
    expect($response['data'])
        ->title->toBe('Fuel')
        ->amount->toBe(5000)
        ->category->toBe('Transportation');
});

test('updates an existing expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
        'title' => 'Old Title',
    ]);

    $response = $this->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertOk();
    expect($response['data']['title'])->toBe('Updated Title');
});

test('employee can\'t update an existing expense', function () {
    $user = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Employee',
    ]);
    Sanctum::actingAs($user, ['*']);

    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'company_id' => $this->company->id,
        'title' => 'Old Title',
    ]);

    $response = $this->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
        ]);
});

test('audit log is created on update of existing expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
        'title' => 'Old Title',
    ]);

    $response = $this->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertOk();
    expect($response['data']['title'])->toBe('Updated Title');

    $audit = AuditLog::all();
    expect($audit)->toHaveCount(1);
    expect($audit[0])
        ->action->toBe('updated')
        ->changes->old->title->toBe('Old Title')
        ->changes->new->title->toBe('Updated Title')
        ->changes->difference->title->toBe('Updated Title')
        ->user_id->toBe($this->user->id)
        ->company_id->toBe($this->company->id);
});

test('deletes an expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
    ]);

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

test('only admin can delete an expense', function () {
    $employeeUser = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Employee',
    ]);
    Sanctum::actingAs($employeeUser, ['*']);

    $expense = Expense::factory()->create([
        'user_id' => $employeeUser->id,
        'company_id' => $this->company->id,
    ]);

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
        ]);

    $this->assertDatabaseHas('expenses', ['id' => $expense->id]);

    $managerUser = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Manager',
    ]);
    Sanctum::actingAs($managerUser, ['*']);

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
        ]);
});

test('audit log is created on delete of an expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->company->id,
    ]);

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);

    $audit = AuditLog::all();
    expect($audit)->toHaveCount(1);
    expect($audit[0])
        ->action->toBe('deleted')
        ->changes->new->toBeNull()
        ->changes->difference->toBeNull()
        ->user_id->toBe($this->user->id)
        ->company_id->toBe($this->company->id);
});
