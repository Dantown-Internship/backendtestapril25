<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['The provided email does not exist.']
                ]
            ]);
    }

    public function test_admin_can_register_new_user(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_id' => $company->id,
            'role' => 'Employee',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);
    }

    public function test_non_admin_cannot_register_new_user(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_id' => $company->id,
            'role' => 'Employee',
        ]);

        $response->assertStatus(403);
    }
}
