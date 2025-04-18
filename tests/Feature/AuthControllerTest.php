<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_validation_errors_on_invalid_input()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['company_name', 'company_email', 'name', 'email', 'password']);
    }

    /** @test */
    public function it_creates_company_and_admin_user_with_valid_data()
    {
        $payload = [
            'company_name' => 'Acme Inc.',
            'company_email' => 'company@example.com',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'company_id', 'role'],
                         'company' => ['id', 'name', 'email']
                     ]
                 ]);

        $this->assertDatabaseHas('companies', [
            'email' => 'company@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'Admin',
        ]);
    }

    /** @test */
    public function it_fails_if_email_already_exists()
    {
        Company::factory()->create(['email' => 'company@example.com']);
        User::factory()->create(['email' => 'john@example.com']);

        $payload = [
            'company_name' => 'Acme Inc.',
            'company_email' => 'company@example.com',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['company_email', 'email']);
    }

    /** @test */
    public function it_can_register_a_user_and_company()
    {
        $data = [
            'company_name' => 'Test Company',
            'company_email' => 'test@company.com',
            'name' => 'Admin User',
            'email' => 'admin@user.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'company_id', 'role'],
                         'company' => ['id', 'name', 'email']
                     ]
                 ]);
    }

    /** @test */
    public function it_can_login_a_user()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'company_id', 'role'],
                         'token'
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_login_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@user.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Invalid credentials',
                     'errors' => [],
                 ]);
    }
}