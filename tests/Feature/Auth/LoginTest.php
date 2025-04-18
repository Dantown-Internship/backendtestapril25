<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('Password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'Password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Logged in successfully')
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'role',
            ],
            'token',
        ]);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('Password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Invalid credentials');
});

test('login requires validation', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'not-an-email',
        'password' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});
