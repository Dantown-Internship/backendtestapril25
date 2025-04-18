<?php

use App\Enums\ExpenseCategory;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;

test('users can update their own expenses', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Employee,
    ]);

    $expense = Expense::factory()->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'title' => 'Original Title',
        'amount' => 100,
        'category' => ExpenseCategory::Food->value,
    ]);

    $response = $this->actingAs($user)->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Updated Title',
        'amount' => 150.75,
        'category' => ExpenseCategory::Transportation->value,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Expense updated successfully')
        ->assertJsonPath('data.title', 'Updated Title')
        ->assertJsonPath('data.amount', 150.75)
        ->assertJsonPath('data.category', ExpenseCategory::Transportation->value);

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'title' => 'Updated Title',
        'amount' => 15075, // Stored as cents in the database
    ]);
});

test('admins can update any expense in their company', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    $employee = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Employee,
    ]);

    $expense = Expense::factory()->create([
        'company_id' => $company->id,
        'user_id' => $employee->id,
    ]);

    $response = $this->actingAs($admin)->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Admin Updated',
        'amount' => 200,
        'category' => ExpenseCategory::Utilities->value,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Admin Updated');
});

test('users cannot update expenses from another company', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = User::factory()->create([
        'company_id' => $company1->id,
        'role' => UserRole::Admin,
    ]);

    $expense = Expense::factory()->create([
        'company_id' => $company2->id,
    ]);

    $response = $this->actingAs($user1)->putJson("/api/expenses/{$expense->id}", [
        'title' => 'Should Not Update',
        'amount' => 999,
        'category' => ExpenseCategory::Others->value,
    ]);

    $response->assertStatus(403);
});
