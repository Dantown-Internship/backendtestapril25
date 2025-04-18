<?php

use App\Enums\ExpenseCategory;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

test('users can create expenses', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    $response = $this->actingAs($user)->postJson('/api/expenses', [
        'title' => 'New Office Equipment',
        'amount' => 299.99,
        'category' => ExpenseCategory::Shopping->value,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Expense created successfully')
        ->assertJsonPath('data.title', 'New Office Equipment')
        ->assertJsonPath('data.amount', 299.99)
        ->assertJsonPath('data.category', ExpenseCategory::Shopping->value);

    $this->assertDatabaseHas('expenses', [
        'title' => 'New Office Equipment',
        'amount' => 29999, // Stored as cents in the database
        'company_id' => $company->id,
        'user_id' => $user->id,
    ]);
});

test('expense creation requires validation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/expenses', [
        'title' => '',
        'amount' => 'not-a-number',
        'category' => 'invalid-category',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'amount', 'category']);
});

test('unauthenticated users cannot create expenses', function () {
    $response = $this->postJson('/api/expenses', [
        'title' => 'New Expense',
        'amount' => 100,
        'category' => ExpenseCategory::Food->value,
    ]);

    $response->assertStatus(401);
});
