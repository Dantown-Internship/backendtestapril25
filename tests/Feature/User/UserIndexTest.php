<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

test('admins can view all users in their company', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    User::factory()->count(5)->create([
        'company_id' => $company->id,
    ]);

    // Create users for another company (should not be visible)
    $anotherCompany = Company::factory()->create();
    User::factory()->count(3)->create([
        'company_id' => $anotherCompany->id,
    ]);

    $response = $this->actingAs($admin)->getJson('/api/users');

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Users fetched successfully')
        ->assertJsonCount(6, 'data'); // 5 created users + 1 admin
});

test('employees cannot view users', function () {
    $company = Company::factory()->create();
    $employee = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Employee,
    ]);

    User::factory()->count(3)->create([
        'company_id' => $company->id,
    ]);

    $response = $this->actingAs($employee)->getJson('/api/users');

    $response->assertStatus(403);
});

test('unauthenticated users cannot view users', function () {
    $response = $this->getJson('/api/users');

    $response->assertStatus(401);
});
