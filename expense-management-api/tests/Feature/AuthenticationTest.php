<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_and_company_can_register(): void
    {
        $payload = [
            'name' => 'George Enesi',
            'email' => 'genesi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'George Innovation',
            'company_email' => 'genetic@example.com',
        ];

        $response = $this->postJson(route('register.user'), $payload);

        $response->assertCreated();

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'genesi@example.com',
        ]);

        $this->assertDatabaseHas('companies', [
            'email' => 'genetic@example.com',
        ]);
        $response->dump();
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'genesi@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'genesi@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('login.user'), $payload);

        $response->assertAccepted();

        $response->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token'
            ]
        ]);
        $response->dump();
    }

    public function test_user_cannot_login_with_invalid_credential(): void
    {
        User::factory()->create([
            'email' => 'genesi@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'genesi@example.com',
            'password' => 'password122',
        ];

        $response = $this->postJson(route('login.user'), $payload);

        $response->assertUnprocessable();
        $response->dump();
    }
}
