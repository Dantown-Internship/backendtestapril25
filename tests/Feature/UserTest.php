<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_users_are_retrieved_and_viewd_by_admin_only(): void
    {
        User::factory()->count(10)->create();
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson(route('users.index'));

        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 11);
    }

    public function test_only_admins_can_create_users(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $company = Company::factory()->create();

        $data = [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'company_id' => $company->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::EMPLOYEE,
        ];


        $token = $admin->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('users.store'), $data);

        $response->assertStatus(201);
        $this->assertCount(2, User::all());
    }

    public function test_only_admins_can_change_user_role(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $company = Company::factory()->create();

        $data = [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'company_id' => $company->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::EMPLOYEE,
        ];

        $token = $admin->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('users.store',), $data);

        $response->assertStatus(201);
        $this->assertCount(2, User::all());

        $updateData = [
            'role' => UserRole::MANAGER,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson(route('users.update', 1), $updateData);

        $response->assertStatus(200);
        $this->assertCount(2, User::all());
    }

    public function test_managers_cannot_create_user(): void
    {
        $manager = User::factory()->create(['role' => UserRole::MANAGER]);
        $company = Company::factory()->create();

        $data = [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'company_id' => $company->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::EMPLOYEE,
        ];

        $token = $manager->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('users.store',), $data);

        $response->assertStatus(403);
        $this->assertCount(1, User::all());
    }

    public function test_employees_cannot_create_user(): void
    {
        $employee = User::factory()->create(['role' => UserRole::EMPLOYEE]);
        $company = Company::factory()->create();

        $data = [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'company_id' => $company->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::EMPLOYEE,
        ];

        $token = $employee->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('users.store',), $data);

        $response->assertStatus(403);
        $this->assertCount(1, User::all());
    }
}
