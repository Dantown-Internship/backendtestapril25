<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

test('admins can create users in their company', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Admin,
    ]);

    $response = $this->actingAs($admin)->postJson('/api/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'pass!Word',
        'role' => UserRole::Employee->value,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'User created successfully')
        ->assertJsonPath('data.name', 'New User')
        ->assertJsonPath('data.email', 'newuser@example.com')
        ->assertJsonPath('data.role', UserRole::Employee->title());

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'company_id' => $company->id,
    ]);
});


test('managers cannot create admin users', function () {
    $company = Company::factory()->create();
    $manager = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Manager,
    ]);

    $response = $this->actingAs($manager)->postJson('/api/users', [
        'name' => 'Attempted Admin',
        'email' => 'admin@example.com',
        'password' => 'pass!Word',
        'role' => UserRole::Admin->value,
    ]);

    $response->assertStatus(403);
});

test('employees cannot create users', function () {
    $company = Company::factory()->create();
    $employee = User::factory()->create([
        'company_id' => $company->id,
        'role' => UserRole::Employee,
    ]);

    $response = $this->actingAs($employee)->postJson('/api/users', [
        'name' => 'New User',
        'email' => 'user@example.com',
        'password' => 'pass!Word',
        'role' => UserRole::Employee->value,
    ]);

    $response->assertStatus(403);
});

test('user creation requires validation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->postJson('/api/users', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'role' => 999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
});
