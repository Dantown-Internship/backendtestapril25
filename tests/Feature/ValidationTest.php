<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $company;
    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => UserRole::ADMIN->value
        ]);

        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_register_validation_errors()
    {
        // Missing required fields
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'company_name', 'company_email']);

        // Invalid email formats
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'company_name' => 'Test Company',
            'company_email' => 'not-an-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'company_email']);

        // Password mismatch
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'company_name' => 'Test Company',
            'company_email' => 'company@test.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Password too short
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'company_name' => 'Test Company',
            'company_email' => 'company@test.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Email already exists
        User::factory()->create([
            'email' => 'existing@email.com',
            'company_id' => $this->company->id
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'existing@email.com', // Already exists
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Test Company',
            'company_email' => 'company@test.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_errors()
    {
        // Missing email and password
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Invalid credentials
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.credentials.0', 'The provided credentials are incorrect.');
    }

    public function test_expense_creation_validation()
    {
        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'amount', 'category']);

        // Invalid amount (negative)
        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'title' => 'Business Lunch',
                'amount' => -50.00,
                'category' => 'Food'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        // Title too long
        $longTitle = str_repeat('a', 300); // More than 255 characters
        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'title' => $longTitle,
                'amount' => 50.00,
                'category' => 'Food'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_creation_validation()
    {
        // Try to create user with invalid role
        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'role' => 'InvalidRole'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_company_update_validation()
    {
        // Try to update with invalid email
        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/company', [
                'name' => 'Updated Company',
                'email' => 'not-an-email'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Try to update with existing email from another company
        $anotherCompany = Company::factory()->create([
            'email' => 'taken@email.com'
        ]);

        $response = $this->actingAs($this->admin)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/company', [
                'name' => 'Updated Company',
                'email' => 'taken@email.com'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
