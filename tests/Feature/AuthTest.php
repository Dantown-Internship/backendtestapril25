<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_company(): void
    {
        $response = $this->postJson('/api/register-company', [
            'company_name' => 'Test Company',
            'company_email' => 'company@test.com',
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'company',
            'user',
            'token',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'email' => 'company@test.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
    }

    public function test_login(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create([
            'company_id' => $company->id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'user',
            'token',
        ]);
    }
    
    public function test_verify(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);
        
        $response = $this->actingAs($user)
                         ->getJson('/api/verify');
                         
        $response->assertOk();
        $response->assertJsonStructure([
            'user',
            'authenticated',
        ]);
        
        $response->assertJson([
            'authenticated' => true,
        ]);
    }

    public function test_logout(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');
                         
        $response->assertOk();
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
        
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
} 