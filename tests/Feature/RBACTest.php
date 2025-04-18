<?php
// ✅ Feature Test 2: Role-Based Access Control (RBAC)
// This test will check the role-based access control for different user roles (Admin, Manager, Employee) in the application.

use App\Enums\Roles;
use App\Models\Company;
use App\Models\User;
use App\Models\Expense;

beforeEach(function () {
    $this->company = Company::factory()->create();

    $this->admin = User::factory()->create([
        'role' => Roles::ADMIN,
        'company_id' => $this->company->id,
    ]);

    $this->manager = User::factory()->create([
        'role' => Roles::MANAGER,
        'company_id' => $this->company->id,
    ]);

    $this->employee = User::factory()->create([
        'role' => Roles::EMPLOYEE,
        'company_id' => $this->company->id,
    ]);

    $this->expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->manager->id,
    ]);
});

// 👑 Admin can delete expenses
it('allows admin to delete an expense', function () {
    $response = $this->withToken($this->admin->createToken('auth')->plainTextToken)
        ->deleteJson("/api/expenses/{$this->expense->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Expense deleted successfully']);
});

// ❌ Manager cannot delete expenses
it('prevents manager from deleting an expense', function () {
    $response = $this->withToken($this->manager->createToken('auth')->plainTextToken)
        ->deleteJson("/api/expenses/{$this->expense->id}");

    $response->assertStatus(403);
});

// ✏️ Manager can update expenses
it('allows manager to update an expense', function () {
    $response = $this->withToken($this->manager->createToken('auth')->plainTextToken)
        ->putJson("/api/expenses/{$this->expense->id}", [
            'title' => 'Updated by Manager',
            'amount' => 150,
            'category' => 'Meals',
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => 'Updated by Manager']);
});

// ❌ Employee cannot update expenses
it('prevents employee from updating an expense', function () {
    $response = $this->withToken($this->employee->createToken('auth')->plainTextToken)
        ->putJson("/api/expenses/{$this->expense->id}", [
            'title' => 'Hacked by Employee',
            'amount' => 999,
            'category' => 'Misc',
        ]);

    $response->assertStatus(403);
});

// ✅ Employee can create their own expenses
it('allows employee to create their own expense', function () {
    $response = $this->withToken($this->employee->createToken('auth')->plainTextToken)
        ->postJson('/api/expenses', [
            'title' => 'Lunch',
            'amount' => 50,
            'category' => 'Food',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['title' => 'Lunch']);
});
