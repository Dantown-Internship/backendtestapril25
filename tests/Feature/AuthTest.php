<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_account_can_be_registered(): void
    {
        $userData = User::factory()->make(['role' => UserRole::ADMIN]);

        $response = $this->postJson(route('register'), array_merge(
            $userData->toArray(),
            [
                'password' => 'Jgs87330))832',
                'password_confirmation' => 'Jgs87330))832',
            ]
        ));

        $user = User::first();

        $response->assertStatus(201);
        $this->assertCount(1, User::all());
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'API TOKEN',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => $user->email,
        ]);
    }
}
