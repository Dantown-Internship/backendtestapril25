<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Employee'
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                    'role' => 'Employee'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_user_role()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'role' => 'Manager',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Users updated successfully',
                'data' => [
                    'id' => $user->id,
                    'role' => 'Manager'
                ]
            ]);
    }
}
