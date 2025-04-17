<?php

use App\Models\Company;
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

test('fetches a list of users for the authenticated user\'s company', function () {
    User::factory()->count(5)->create([
        'company_id' => $this->company->id,
    ]);

    $response = $this->getJson('/api/users');

    $response->assertOk();
    expect($response['data'])->toHaveCount(6);
});

test('authenticated user can\'t see other company\'s expenses', function () {
    User::factory()->count(5)->create([
        'company_id' => $this->company->id,
    ]);

    $anotherCompany = Company::factory()->create();
    User::factory(3)->create([
        'company_id' => $anotherCompany->id,
    ]);

    $response = $this->getJson('/api/users');

    $response->assertOk();

    expect(User::all())->toHaveCount(9);
    expect($response['data'])->toHaveCount(6);
});

test('creates a new user', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'Jane Doe',
        'email' => 'doe@gmail.com',
        'role' => 'Manager',
        'password' => 'password',
    ]);

    $response->assertCreated();
    expect($response['data'])
        ->name->toBe('Jane Doe')
        ->email->toBe('doe@gmail.com')
        ->role->toBe('Manager');
});

test('update an existing user role', function () {
    $user = User::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Jane Doe',
        'email' => 'doe@gmail.com',
        'role' => 'Manager',
        'password' => 'password',
    ]);

    $response = $this->putJson("/api/users/{$user->id}", [
        'role' => 'Employee',
    ]);

    $response->assertOk();
    expect($response['data']['role'])->toBe('Employee');
});

test('only admin can update an existing user role', function () {
    $user = User::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Jane Doe',
        'email' => 'doe@gmail.com',
        'role' => 'Manager',
        'password' => 'password',
    ]);

    $employeeUser = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Employee',
    ]);

    Sanctum::actingAs($employeeUser, ['*']);

    $response = $this->putJson("/api/users/{$user->id}", [
        'role' => 'Employee',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
        ]);

    expect(User::find($user->id)->isManager())->toBeTrue();

    $managerUser = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'Manager',
    ]);
    Sanctum::actingAs($managerUser, ['*']);

    $response = $this->putJson("/api/users/{$user->id}", [
        'role' => 'Employee',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
        ]);

    expect(User::find($user->id)->isManager())->toBeTrue();

});
