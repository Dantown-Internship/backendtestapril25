<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $company;
    private $admin;
    private $manager;
    private $employee;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Admin',
        ]);
        $this->manager = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Manager',
        ]);
        $this->employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee',
        ]);
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_view_all_users(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_manager_cannot_view_all_users(): void
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_update_user_role(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$this->employee->id}", [
            'role' => 'Manager',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'role' => 'Manager',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->employee->id,
            'role' => 'Manager',
        ]);
    }

    public function test_admin_cannot_update_user_role_to_invalid_value(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$this->employee->id}", [
            'role' => 'InvalidRole',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_user_password(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$this->employee->id}/password", [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_cannot_update_password_with_invalid_current_password(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$this->employee->id}/password", [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_cannot_update_password_with_mismatched_confirmation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$this->employee->id}/password", [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422);
    }
}
