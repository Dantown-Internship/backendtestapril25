<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

test('admins can update users in their company', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    $user = User::factory()->create([
        'company_id' => $company->id,
        'name' => 'Original Name',
        'role' => UserRole::Employee->value,
    ]);

    $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
        'name' => 'Updated Name',
        'role' => UserRole::Manager->value,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'User updated successfully')
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.role', UserRole::Manager->title());

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'role' => UserRole::Manager->value,
    ]);
});

test('Non-admin users cannot update users in their company', function () {
    $company = Company::factory()->create();
    $manager = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Manager,
    ]);

    $employee = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Employee->value,
        'name' => 'Old Employee Name',
    ]);

    $response = $this->actingAs($manager)->putJson("/api/users/{$employee->id}", [
        'name' => 'New Employee Name',
    ]);

    $response->assertStatus(403);
});

test('users cannot update users from another company', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $admin1 = User::factory()->create([
        'company_id' => $company1->id,
        'role' => UserRole::Admin,
    ]);

    $user2 = User::factory()->create([
        'company_id' => $company2->id,
        'name' => 'Original Name',
    ]);

    $response = $this->actingAs($admin1)->putJson("/api/users/{$user2->id}", [
        'name' => 'Should Not Update',
    ]);

    $response->assertStatus(403);
});
