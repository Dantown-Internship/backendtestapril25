<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('users can authenticate using the login endpoint', function () {
    $user = User::factory()->create();
    $response = postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user',
                'token' => [
                    'access_token',
                    'token_type',
                    'expires_in'
                ]
            ]
        ]);
    $userData = $response->json()['data']['user'];
    expect($userData['id'])->toBe($user->uuid);
    expect($userData['email'])->toBe($user->email);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
    $response->assertJson([
        'status' => false
    ])->assertStatus(422);
});
