<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_login_with_valid_credentials()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id', 'name', 'email', 'role', 'company_id'
                ]
            ]);
    }

    /** @test */
    public function a_user_cannot_login_with_invalid_credentials()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);
    }

    /** @test */
    public function an_admin_can_register_new_users()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id
        ]);

        $this->actingAs($admin);

        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'Employee'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'name', 'email', 'role', 'company_id'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
    }

    /** @test */
    public function non_admin_users_cannot_register_new_users()
    {
        $company = Company::factory()->create();
        $employee = User::factory()->create([
            'role' => 'Employee',
            'company_id' => $company->id
        ]);

        $this->actingAs($employee);

        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'Employee'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('users', [
            'email' => 'newuser@example.com'
        ]);
    }
} 