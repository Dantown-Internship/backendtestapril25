<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::factory()->create();

        $this->admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
        $this->adminToken = $this->admin->createToken('API Token')->plainTextToken;

        $this->manager = User::factory()->create([
            'role' => 'Manager',
            'company_id' => $company->id,
        ]);
        $this->managerToken = $this->manager->createToken('API Token')->plainTextToken;
    }


    public function test_admin_can_create_user(): void
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new1@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'Employee',
            'company_id' => $this->admin->company_id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                "success" => true,
                "message" => "User saved successfully",
            ]);
    }

    public function test_manager_cannot_create_user(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->managerToken,
        ])->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'Employee',
            'company_id' => $this->admin->company_id,
        ]);

        $response->assertStatus(500);
    }
}
