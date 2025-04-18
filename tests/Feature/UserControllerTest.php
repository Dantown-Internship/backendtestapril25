<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_register_with_company()
    {
        $response = $this->postJson('/api/register', [
            'company_name' => 'Test Company',
            'company_email' => 'company@example.com',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'meta' => ['user', 'token']
            ]);

        $this->assertDatabaseHas('companies', ['email' => 'company@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
    }

    public function test_user_can_login_and_get_token()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Admin'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'meta' => ['user', 'token']
            ]);
    }



    public function test_admin_can_access_users_index()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_users_index()
    {
        $user = User::factory()->create(['role' => 'Employee']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(403); // Forbidden by `role:Admin` middleware
    }
}