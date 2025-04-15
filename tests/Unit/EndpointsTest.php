<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class EndpointsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user registration endpoint
     */
    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);
    }

    /**
     * Test user login endpoint
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);
    }

    /**
     * Test expense listing endpoint
     */
    public function test_authenticated_user_can_list_expenses()
    {
        $user = User::factory()->create();
        Expense::factory()->count(3)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/expenses');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test expense creation endpoint
     */
    public function test_authenticated_user_can_create_expense()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $expenseData = [
            'description' => 'Test Expense',
            'amount' => 100.50,
            'date' => now()->format('Y-m-d'),
            'category' => 'Office Supplies'
        ];

        $response = $this->postJson('/api/expenses', $expenseData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'description', 'amount', 'date', 'category']
            ]);
    }

    /**
     * Test expense update endpoint with admin role
     */
    public function test_admin_can_update_expense()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $expense = Expense::factory()->create();

        Sanctum::actingAs($admin);

        $updateData = [
            'description' => 'Updated Expense',
            'amount' => 200.75
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.description', 'Updated Expense')
            ->assertJsonPath('data.amount', 200.75);
    }

    /**
     * Test expense update endpoint with manager role
     */
    public function test_manager_can_update_expense()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $expense = Expense::factory()->create();

        Sanctum::actingAs($manager);

        $updateData = [
            'description' => 'Manager Updated Expense',
            'amount' => 150.25
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

        $response->assertStatus(200);
    }

    /**
     * Test expense update endpoint with regular user (unauthorized)
     */
    public function test_regular_user_cannot_update_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $expense = Expense::factory()->create();

        Sanctum::actingAs($user);

        $updateData = [
            'description' => 'Unauthorized Update',
            'amount' => 50.00
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

        $response->assertStatus(403);
    }

    /**
     * Test expense deletion endpoint with admin role
     */
    public function test_admin_can_delete_expense()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $expense = Expense::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    /**
     * Test expense deletion endpoint with non-admin role (unauthorized)
     */
    public function test_non_admin_cannot_delete_expense()
    {
        $user = User::factory()->create(['role' => 'user']);
        $expense = Expense::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
    }

    /**
     * Test user listing endpoint with admin role
     */
    public function test_admin_can_list_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(6, 'data'); // 5 created users + 1 admin
    }

    /**
     * Test user listing endpoint with non-admin role (unauthorized)
     */
    public function test_non_admin_cannot_list_users()
    {
        $user = User::factory()->create(['role' => 'user']);
        User::factory()->count(5)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    /**
     * Test user creation endpoint with admin role
     */
    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $userData = [
            'name' => 'New Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Test User')
            ->assertJsonPath('data.email', 'newuser@example.com');
    }

    /**
     * Test user creation endpoint with non-admin role (unauthorized)
     */
    public function test_non_admin_cannot_create_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $userData = [
            'name' => 'Unauthorized User Creation',
            'email' => 'unauthorized@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(403);
    }

    /**
     * Test user update endpoint with admin role
     */
    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $updateData = [
            'name' => 'Updated User Name',
            'role' => 'manager'
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated User Name')
            ->assertJsonPath('data.role', 'manager');
    }

    /**
     * Test user update endpoint with non-admin role (unauthorized)
     */
    public function test_non_admin_cannot_update_user()
    {
        $regularUser = User::factory()->create(['role' => 'user']);
        $targetUser = User::factory()->create();

        Sanctum::actingAs($regularUser);

        $updateData = [
            'name' => 'Unauthorized Update',
            'role' => 'admin'
        ];

        $response = $this->putJson("/api/users/{$targetUser->id}", $updateData);

        $response->assertStatus(403);
    }
}
