<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company and user for testing
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => UserRole::ADMIN
        ]);

        // Generate token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_get_company_details()
    {
        $response = $this->actingAs($this->user)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/company');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Company details retrieved successfully',
                'data' => [
                    'company' => [
                        'id' => $this->company->id,
                        'name' => $this->company->name
                    ]
                ]
            ]);
    }

    public function test_can_update_company_details()
    {
        $updatedData = [
            'name' => 'Updated Company Name',
            'email' => 'updated@example.com'  // Adding the required email field
        ];

        $response = $this->actingAs($this->user)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/company', $updatedData);

        $response->assertStatus(200)
            ->assertJsonPath('data.company.name', 'Updated Company Name');

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name'
        ]);
    }

    public function test_can_get_company_statistics()
    {
        // Create some expenses for the company to generate statistics
        \App\Models\Expense::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'category' => 'Food'
        ]);

        \App\Models\Expense::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'category' => 'Travel'
        ]);

        $response = $this->actingAs($this->user)
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/company/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_expenses',
                    'user_count',
                    'expense_count',
                    'recent_expenses',
                    'expenses_by_category'
                ]
            ]);

        // Verify counts match what we created
        $response->assertJsonPath('data.expense_count', 8);
        $response->assertJsonPath('data.user_count', 1);
    }

    public function test_unauthorized_user_cannot_access_company_details()
    {
        // Create a user from a different company
        $otherCompany = Company::factory()->create();
        $otherUser = User::factory()->create([
            'company_id' => $otherCompany->id
        ]);

        $otherToken = $otherUser->createToken('test-token')->plainTextToken;

        // Try to access the first company's details with the other user's token
        $response = $this->actingAs($otherUser)
            ->withHeader('Authorization', 'Bearer ' . $otherToken)
            ->getJson('/api/company');

        // Should return company of the authenticated user, not the original company
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Company details retrieved successfully',
                'data' => [
                    'company' => [
                        'id' => $otherCompany->id,
                        'name' => $otherCompany->name
                    ]
                ]
            ]);
    }
}
