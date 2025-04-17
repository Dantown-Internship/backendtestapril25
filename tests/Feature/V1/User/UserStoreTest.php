<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can create a new user assigned to their company', function() {
    $admin = User::factory()->admin()->create()->fresh();
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'role' => Role::Employee->value,
        'password' => "password",
        'password_confirmation' => "password",
    ];
    $response = actingAs($admin)
        ->postJson(route('api.v1.users.store'), $userData)
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => "New User",
                'email' => 'newuser@example.com',
                'role' => Role::Employee->value,
            ]
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'created_at',
                'updated_at'
            ]
        ]);

    $userUid = $response->json('data')['id'];
    expect(User::where('uuid', $userUid)->first()?->company_id)
        ->toBe($admin->company_id);
});

test('validation error is thrown when invalid input is supplied', function () {
    $admin = User::factory()->admin()->create()->fresh();
    $userData = [
        'name' => '',
        'email' => 'invalid-email',
        'role' => 'invalid-role',
        'password' => "short",
        'password_confirmation' => "different",
    ];

    actingAs($admin)
        ->postJson(route('api.v1.users.store'), $userData)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'role', 'password']);
});
