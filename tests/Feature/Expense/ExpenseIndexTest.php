<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;

test('authenticated users can view expenses in their company', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    Expense::factory()->count(5)->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
    ]);

    // Create expenses for another company (should not be visible)
    $anotherCompany = Company::factory()->create();
    Expense::factory()->count(3)->create([
        'company_id' => $anotherCompany->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/expenses');

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Expenses fetched successfully')
        ->assertJsonCount(5, 'data');
});

test('users can search expenses by title or category', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    Expense::factory()->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'title' => 'Office Supplies',
    ]);

    Expense::factory()->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'title' => 'Team Lunch',
    ]);

    $response = $this->actingAs($user)->getJson('/api/expenses?search=Supplies');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Office Supplies');
});

test('unauthenticated users cannot view expenses', function () {
    $response = $this->getJson('/api/expenses');

    $response->assertStatus(401);
});
