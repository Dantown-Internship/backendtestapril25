<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admins_can_view_users_from_their_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $admin = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'Admin'
        ]);
        
        // Create users for admin's company
        User::factory()->count(5)->create([
            'company_id' => $company1->id
        ]);
        
        // Create users for different company
        User::factory()->count(3)->create([
            'company_id' => $company2->id
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->getJson('/api/users');
        
        $response->assertStatus(200)
            ->assertJsonCount(6, 'data'); // 5 + the admin
    }
    
    /** @test */
    public function non_admins_cannot_view_users()
    {
        $company = Company::factory()->create();
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Manager'
        ]);
        
        User::factory()->count(5)->create([
            'company_id' => $company->id
        ]);
        
        Sanctum::actingAs($manager);
        
        $response = $this->getJson('/api/users');
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function admins_can_add_users_to_their_company()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'Manager'
        ]);
        
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'Manager',
            'company_id' => $company->id
        ]);
    }
    
    /** @test */
    public function admins_can_update_user_role()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'Manager'
        ]);
    }
    
    /** @test */
    public function non_admins_cannot_update_user_role()
    {
        $company = Company::factory()->create();
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Manager'
        ]);
        
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        Sanctum::actingAs($manager);
        
        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'Employee'
        ]);
    }
    
    /** @test */
    public function cannot_access_users_from_different_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $admin = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'Admin'
        ]);
        
        $user = User::factory()->create([
            'company_id' => $company2->id,
            'role' => 'Employee'
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'Employee'
        ]);
    }
} 