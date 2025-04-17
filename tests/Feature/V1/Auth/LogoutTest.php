<?php

use App\Models\User;


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
