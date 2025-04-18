<?php
// ✅ Feature Test 3: Multi-Tenant Access Control
// This test ensures that users can only access and modify expenses within their own company.

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Enums\Roles;

beforeEach(function () {
    $this->companyA = Company::factory()->create();
    $this->companyB = Company::factory()->create();

    $this->userA = User::factory()->create([
        'role' => Roles::EMPLOYEE,
        'company_id' => $this->companyA->id,
    ]);

    $this->userB = User::factory()->create([
        'role' => Roles::MANAGER,
        'company_id' => $this->companyB->id,
    ]);

    $this->expenseA = Expense::factory()->create([
        'company_id' => $this->companyA->id,
        'user_id' => $this->userA->id,
    ]);

    $this->expenseB = Expense::factory()->create([
        'company_id' => $this->companyB->id,
        'user_id' => $this->userB->id,
    ]);
});

// ❌ Prevent access to another company's expense
it('prevents users from accessing another company’s expense', function () {
    $token = $this->userA->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson("/api/expenses/{$this->expenseB->id}");

    $response->assertStatus(405); // or 404 depending on how you handle hidden resources
});

// ❌ Prevent updates across companies
it('prevents cross-company expense updates', function () {
    $token = $this->userB->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)
        ->putJson("/api/expenses/{$this->expenseA->id}", [
            'title' => 'Illegal Update',
            'amount' => 500,
            'category' => 'Misc',
        ]);

    $response->assertStatus(403);
});

// ❌ Prevent deletes across companies
it('prevents users from deleting another company’s expense', function () {
    $token = $this->userB->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)
        ->deleteJson("/api/expenses/{$this->expenseA->id}");

    $response->assertStatus(403);
});