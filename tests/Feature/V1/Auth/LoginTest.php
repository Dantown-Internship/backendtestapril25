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

test("user's token gets deleted on logout", function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;
    // Make the request with the token
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson(route('api.v1.logout'));

    // Assert the response
    $response
        ->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Logout successful',
        ]);

    // Assert the token was deleted
    expect($user->tokens->count())->toBe(0);
});
