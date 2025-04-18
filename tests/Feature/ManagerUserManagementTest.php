<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ManagerUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->manager = User::factory()->create([
            'role' => 'Manager',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->manager);
    }

    public function test_manager_cannot_list_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_manager_cannot_create_user()
    {
        $payload = [
            'name' => 'Unauthorized User',
            'email' => 'unauth@example.com',
            'password' => 'password',
            'role' => 'Employee'
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(403);
    }

    public function test_manager_cannot_update_user()
    {
        $user = User::factory()->create([
            'company_id' => $this->manager->company_id,
            'role' => 'Employee'
        ]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);

        $response->assertStatus(403);
    }
}
