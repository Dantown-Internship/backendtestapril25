<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registers a company account and user successfully', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Acme Inc.',
        'company_email' => 'acme@example.com',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                ],
                'token',
            ],
        ]);

    $user = User::where('email', 'john@example.com');

    expect($user->exists())->toBeTrue();
    expect($user->first()->company->name)->toBe('Acme Inc.');
    expect($user->first()->company->email)->toBe('acme@example.com');
});

test('registers only admin user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Acme Inc.',
        'company_email' => 'acme@example.com',
    ]);

    $response->assertCreated();

    $user = User::where('email', 'john@example.com');

    expect($user->exists())->toBeTrue();
    expect($user->first()->isAdmin())->toBeTrue();
    expect($user->first()->role)->not->toBeIn(['Manager', 'Employee']);
});

test('logs in a registered user successfully', function () {
    $user = User::factory()->for(Company::factory()->create())->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                ],
            ],
        ]);
});

test('fails to login with invalid credentials', function () {
    $user = User::factory()->for(Company::factory()->create())->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ]);
});
