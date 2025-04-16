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
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'company_id',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next'
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total'
                    ]
                ]
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
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$user->id}", [
            'role' => 'Manager',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'company_id'
                ]
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
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
    }

    public function test_admin_cannot_update_password_with_invalid_current_password(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/password', [
            'current_password' => 'wrong-password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Current password is incorrect'
            ]);
    }

    public function test_admin_cannot_update_password_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
