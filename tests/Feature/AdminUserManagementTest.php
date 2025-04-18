<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->admin);
    }

    public function test_admin_can_list_users()
    {
        User::factory()->count(5)->create(['company_id' => $this->admin->company_id]);

        $response = $this->getJson('/api/users');

        $response->assertJsonStructure([
            'data' => [
                ['id', 'name', 'email', 'created_at', 'updated_at'],
            ],
        ]);
    }

    public function test_admin_can_create_user()
    {
        $payload = [
            'name' => 'New Manager',
            'email' => 'manager@example.com',
            'password' => 'password',
            'role' => 'Manager'
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['email' => 'manager@example.com']);
    }

    public function test_admin_can_update_user_role()
    {
        $user = User::factory()->create([
            'company_id' => $this->admin->company_id,
            'role' => 'Employee'
        ]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Role updated successfully.']);
    }

    public function test_admin_cannot_manage_users_from_other_companies()
    {
        $otherCompany = \App\Models\Company::factory()->create();

        $otherUser = User::factory()->create([
            'company_id' => $otherCompany->id,
            'role' => 'Employee'
        ]);

        $response = $this->putJson("/api/users/{$otherUser->id}", [
            'role' => 'Manager'
        ]);

        $response->assertStatus(404);
    }
}
