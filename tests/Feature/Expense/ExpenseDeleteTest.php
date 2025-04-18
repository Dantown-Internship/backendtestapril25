<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;

test('admins can delete any expense in their company', function () {
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

    $response = $this->actingAs($admin)->deleteJson("/api/expenses/{$expense->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

test('unauthenticated users cannot delete expenses', function () {
    $expense = Expense::factory()->create();

    $response = $this->deleteJson("/api/expenses/{$expense->id}");

    $response->assertStatus(401);
    $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
});
