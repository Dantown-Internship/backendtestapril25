<?php

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    // Seed roles and permissions
    $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolesAndPermissionsSeeder']);

    // Create test companies
    $this->companyA = Company::factory()->create(['name' => 'Company A']);
    $this->companyB = Company::factory()->create(['name' => 'Company B']);

    // Create users for Company A
    $this->adminA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => \App\Enums\RoleEnum::ADMIN->value,
    ]);
    $this->adminA->assignRole('admin');

    $this->managerA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => \App\Enums\RoleEnum::MANAGER->value,
    ]);
    $this->managerA->assignRole('manager');

    $this->employeeA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => \App\Enums\RoleEnum::EMPLOYEE->value,
    ]);
    $this->employeeA->assignRole('employee');

    // Create user for Company B
    $this->adminB = User::factory()->create([
        'company_id' => $this->companyB->id,
        'role' => \App\Enums\RoleEnum::ADMIN->value,
    ]);
    $this->adminB->assignRole('admin');

    // Create expenses for Company A
    Expense::factory()->count(5)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->employeeA->id,
        'category' => 'Travel',
    ]);

    Expense::factory()->count(3)->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->managerA->id,
        'category' => 'Office Supplies',
    ]);

    // Create expenses for Company B
    Expense::factory()->count(4)->create([
        'company_id' => $this->companyB->id,
        'user_id' => $this->adminB->id,
    ]);
});

test('user can list expenses for their company only', function () {
    Sanctum::actingAs($this->employeeA);

    $response = $this->getJson('/api/expenses');

    $response->assertOk();

    $expenses = json_decode($response->getContent(), true)['data'];

    // Should see 8 expenses (5 from employeeA + 3 from managerA)
    expect(count($expenses['data']))->toBe(8);

    // All expenses should belong to Company A
    foreach ($expenses['data'] as $expense) {
        expect($expense['company_id'])->toBe($this->companyA->id);
    }
});

test('user can filter expenses by category', function () {
    Sanctum::actingAs($this->employeeA);

    $response = $this->getJson('/api/expenses?category=Travel');

    $response->assertOk();

    $expenses = json_decode($response->getContent(), true)['data'];

    // Should see 5 expenses in Travel category
    expect(count($expenses['data']))->toBe(5);

    // All expenses should have Travel category
    foreach ($expenses['data'] as $expense) {
        expect($expense['category'])->toBe('Travel');
    }
});

test('user can create an expense', function () {
    Sanctum::actingAs($this->employeeA);

    $expenseData = [
        'title' => 'New Laptop',
        'amount' => 1299.99,
        'category' => 'Equipment',
    ];

    $response = $this->postJson('/api/expenses', $expenseData);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'title' => 'New Laptop',
            'amount' => 1299.99,
            'category' => 'Equipment',
            'user_id' => $this->employeeA->id,
            'company_id' => $this->companyA->id,
        ]);

    $this->assertDatabaseHas('expenses', [
        'title' => 'New Laptop',
        'amount' => 1299.99,
        'category' => 'Equipment',
        'user_id' => $this->employeeA->id,
        'company_id' => $this->companyA->id,
    ]);
});

test('manager can update an expense', function () {
    Sanctum::actingAs($this->managerA);

    $expense = Expense::where('company_id', $this->companyA->id)->first();

    $updateData = [
        'title' => 'Updated Expense',
        'amount' => 500.00,
        'category' => 'Travel',
    ];

    $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

    $response->assertOk()
        ->assertJsonFragment([
            'title' => 'Updated Expense',
            'amount' => 500.00,
        ]);

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'title' => 'Updated Expense',
        'amount' => 500.00,
    ]);

    // Check that an audit log was created
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $this->managerA->id,
        'company_id' => $this->companyA->id,
        'action' => 'update_expense',
    ]);
});


test('employee cannot update an expense', function () {
    Sanctum::actingAs($this->employeeA);

    $expense = Expense::where('company_id', $this->companyA->id)->first();

    $updateData = [
        'title' => 'Try to update',
        'amount' => 999.99,
        'category' => 'Travel',
    ];

    $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

    $response->assertStatus(403);

    // Verify expense was not updated
    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
        'title' => 'Try to update',
    ]);
});
test('admin can delete an expenses', function () {
    Sanctum::actingAs($this->adminA);

    $expense = Expense::where('company_id', $this->companyA->id)->first();

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertNoContent();

    // Verify expense was deleted
    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);

    // Check that an audit log was created
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $this->adminA->id,
        'company_id' => $this->companyA->id,
        'action' => 'delete_expense',
    ]);
});

test('non-admin cannot delete an expense', function () {
    Sanctum::actingAs($this->managerA);

    $expense = Expense::where('company_id', $this->companyA->id)->first();

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertStatus(403);

    // Verify expense was not deleted
    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
    ]);
});

test('user cannot access expense from another company', function () {
    Sanctum::actingAs($this->adminA);

    // Get an expense from Company B
    $expenseFromB = Expense::where('company_id', $this->companyB->id)->first();

    $response = $this->getJson("/api/expenses/{$expenseFromB->id}");

    $response->assertStatus(403);
});

test('admin can delete an expense', function () {
    Sanctum::actingAs($this->adminA);

    $expense = Expense::where('company_id', $this->companyA->id)->first();

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);

    // Check that an audit log was created
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $this->adminA->id,
        'company_id' => $this->companyA->id,
        'action' => 'delete_expense',
    ]);
});

