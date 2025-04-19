<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions
    $this->artisan('db:seed', ['--class' => 'Database\Seeders\RolesAndPermissionsSeeder']);
});

test('company and admin can be registered', function () {
    $response = $this->postJson('/api/register', [
        'company_name' => 'Acme Inc',
        'company_email' => 'admin@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'Admin',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ],
        ]);

    $this->assertDatabaseHas('companies', [
        'name' => 'Acme Inc',
        'email' => 'admin@acme.com',
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'role' => 'admin',
    ]);

    // Check if the user has the admin role
    $user = User::where('email', 'john@acme.com')->first();
    expect($user->hasRole('admin'))->toBeTrue();
});

test('user can login', function () {
    // Create a company and user
    $company = Company::factory()->create([
        'name' => 'Test Company',
        'email' => 'info@testcompany.com',
    ]);

    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'company_id' => $company->id,
        'role' => 'Employee',
    ]);

    $user->assignRole('employee');

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data',
        ]);
});

test('user can logout', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'Employee',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'message' => 'User Logged Out Successfully',
        ]);

    // Check that token was deleted
    expect($user->tokens()->count())->toBe(0);
});

