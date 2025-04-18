<?php

use App\Models\User;

test('user can register with valid data', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'pass!Word',
        'password_confirmation' => 'pass!Word',
        'company_name' => 'Test Company',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'User created successfully')
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'company',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('companies', [
        'name' => 'Test Company',
    ]);
});

test('user cannot register with invalid data', function () {
    $response = $this->postJson('/api/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'pass',
        'password_confirmation' => 'different',
        'company_name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'company_name']);
});

test('user cannot register with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'pass!Word',
        'password_confirmation' => 'pass!Word',
        'company_name' => 'Test Company',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
