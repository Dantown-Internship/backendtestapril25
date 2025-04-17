<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
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
        $this->token = $this->employee->createToken('test-token')->plainTextToken;
    }

    public function test_employee_can_create_expense(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/expenses', [
            'title' => 'Test Expense',
            'amount' => 100.50,
            'category' => 'Travel',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'amount',
                    'category',
                    'company_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_employee_can_view_own_expenses(): void
    {
        Expense::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/expenses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'amount',
                            'category',
                            'company_id',
                            'user_id',
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

    public function test_manager_can_update_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
        ]);

        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Expense',
            'amount' => 200.00,
            'category' => 'Equipment',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'amount',
                    'category',
                    'company_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_admin_can_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
        ]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Expense deleted successfully'
            ]);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_employee_cannot_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(403);
    }

    public function test_can_get_expense_summary(): void
    {
        Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
            'amount' => 100.00,
            'category' => 'Travel',
        ]);

        Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->employee->id,
            'amount' => 200.00,
            'category' => 'Equipment',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/expenses/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'total_expenses',
                    'average_expense',
                    'expenses_by_category' => [
                        '*' => [
                            'total',
                            'count'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'data' => [
                    'total_expenses' => 300.00,
                    'average_expense' => 150.00,
                ]
            ]);
    }
}
