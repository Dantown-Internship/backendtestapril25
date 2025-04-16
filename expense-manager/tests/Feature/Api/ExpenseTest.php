<?php

use App\Enums\Roles;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->company = Company::factory()->create();

    $this->admin = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => Roles::ADMIN->value,
    ]);

    $this->manager = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => Roles::MANAGER->value,
    ]);

    $this->employee = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => Roles::EMPLOYEE->value,
    ]);

    $this->token = $this->admin->createToken('test-token')->plainTextToken;
});

test('admin can list expenses', function (): void {
    Expense::factory()->count(3)->create([
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/expenses');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'amount',
                    'category',
                    'created_at',
                    'updated_at',
                    'user',
                ],
            ],
            'total',
            'next_page',
        ]);

    // Pest's expect helper can be used as well:
    expect(count($response->json('data')))->toBe(3);
});

test('employee can only see their own expenses', function (): void {
    // Create expenses for the employee
    Expense::factory()->count(2)->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    // Create some expenses for the admin
    Expense::factory()->count(3)->create([
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
    ]);

    $employeeToken = $this->employee->createToken('employee-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$employeeToken,
    ])->getJson('/api/expenses');

    $response->assertStatus(200);

    expect(count($response->json('data')))->toBe(2);
});

test('admin can create expense', function (): void {
    $expenseData = [
        'title' => 'Test Expense',
        'amount' => 100.50,
        'category' => 'Food',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->postJson('/api/expenses', $expenseData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'amount',
                'category',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('expenses', [
        'title' => 'Test Expense',
        'amount' => 100.50,
        'category' => 'Food',
        'user_id' => $this->admin->id,
        'company_id' => $this->company->id,
    ]);
});

test('manager can update expense', function (): void {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $updateData = [
        'title' => 'Updated Expense',
        'amount' => 200.75,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->putJson('/api/expenses/'.$expense->id, $updateData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'amount',
                'category',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'title' => 'Updated Expense',
        'amount' => 200.75,
    ]);
});

test('employee cannot update expense', function (): void {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
    ]);

    $employeeToken = $this->employee->createToken('employee-token')->plainTextToken;

    $updateData = [
        'title' => 'Updated Expense',
        'amount' => 200.75,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$employeeToken,
    ])->putJson('/api/expenses/'.$expense->id, $updateData);

    $response->assertStatus(403);
});

test('admin can delete expense', function (): void {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->deleteJson('/api/expenses/'.$expense->id);

    $response->assertStatus(204);
    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);
});

test('manager cannot delete expense', function (): void {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->deleteJson('/api/expenses/'.$expense->id);

    $response->assertStatus(403);
    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
    ]);
});

test('user cannot access expenses from other company', function (): void {
    $otherCompany = Company::factory()->create();
    $otherExpense = Expense::factory()->create([
        'company_id' => $otherCompany->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/expenses/'.$otherExpense->id);

    $response->assertStatus(403);
});
