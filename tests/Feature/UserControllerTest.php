<?php

use App\Models\Company;
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
        'role' => 'admin',
    ]);
    $this->adminA->assignRole('admin');

    $this->managerA = User::factory()->create([
        'company_id' => $this->companyA->id,
        'role' => 'manager',
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
        'role' => 'admin',
    ]);
    $this->adminB->assignRole('admin');

    $this->managerB = User::factory()->create([
        'company_id' => $this->companyB->id,
        'role' => 'manager',
    ]);
    $this->managerB->assignRole('manager');

    $this->employeeB = User::factory()->create([
        'company_id' => $this->companyB->id,
        'role' => \App\Enums\RoleEnum::EMPLOYEE->value,
    ]);
    $this->employeeB->assignRole('employee');
});

test('admin can list company users', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($this->adminA);

    $response = $this->getJson('/api/users');

    $response->assertOk();

    $users = json_decode($response->getContent(), true)['data'];

    // Should see 3 users from Company A
    expect(count($users['data']))->toBe(3);

    // All users should belong to Company A
    foreach ($users['data'] as $user) {
        expect($user['company_id'])->toBe($this->companyA->id);
    }
});

test('non-admin cannot list company users', function () {
    Sanctum::actingAs($this->managerA);

    $response = $this->getJson('/api/users');

    $response->assertStatus(403);
});

test('admin can create a new user', function () {
    Sanctum::actingAs($this->adminA);

    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => \App\Enums\RoleEnum::MANAGER->value,
    ];

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => \App\Enums\RoleEnum::MANAGER->value,
            'company_id' => $this->companyA->id,
        ]);

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'role' => \App\Enums\RoleEnum::MANAGER->value,
        'company_id' => $this->companyA->id,
    ]);

    // Check that the user has the manager role
    $user = User::where('email', 'newuser@example.com')->first();
    expect($user->hasRole('manager'))->toBeTrue();
});

test('non-admin cannot create a user', function () {
    Sanctum::actingAs($this->managerA);

    $userData = [
        'name' => 'Attempted User',
        'email' => 'attempt@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => \App\Enums\RoleEnum::EMPLOYEE->value,
    ];

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('users', [
        'email' => 'attempt@example.com',
    ]);
});

test('admin can update a user role', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($this->adminA);

    $updateData = [
        'role' => \App\Enums\RoleEnum::MANAGER->value,
    ];

    $response = $this->putJson("/api/users/{$this->employeeA->id}", $updateData);

    $response->assertOk()
        ->assertJsonFragment([
            'role' => \App\Enums\RoleEnum::MANAGER->value,
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->employeeA->id,
        'role' => \App\Enums\RoleEnum::MANAGER->value,
    ]);

    // Check that the role was updated
    $this->employeeA->refresh();
    expect($this->employeeA->hasRole('manager'))->toBeTrue();
    expect($this->employeeA->hasRole('employee'))->toBeFalse();
});

test('admin cannot update users from another company', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($this->adminA);

    $updateData = [
        'name' => 'Attempted Update',
        'role' => \App\Enums\RoleEnum::EMPLOYEE->value,
    ];

    $response = $this->putJson("/api/users/{$this->employeeB->id}", $updateData);

    $response->assertStatus(403);

    // Verify user was not updated
    $this->assertDatabaseMissing('users', [
        'id' => $this->adminB->id,
        'name' => 'Attempted Update',
    ]);
});

