<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class EmployeeUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->employee = User::factory()->create([
            'role' => 'Employee',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->employee);
    }

    public function test_employee_cannot_list_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_employee_cannot_create_user()
    {
        $payload = [
            'name' => 'Unauthorized User',
            'email' => 'unauth@example.com',
            'password' => 'password',
            'role' => 'Manager'
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(403);
    }

    public function test_employee_cannot_update_user()
    {
        $user = User::factory()->create([
            'company_id' => $this->employee->company_id,
            'role' => 'Employee'
        ]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'role' => 'Manager'
        ]);

        $response->assertStatus(403);
    }
}
