<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    // Authenticate the user using Sanctum's actingAs helper
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Logged out successfully');

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('unauthenticated user cannot logout', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});
