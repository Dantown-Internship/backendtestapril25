<?php
//Feature Test 4: Expenses (CRUD, Validation, Caching Awareness)
//This Test covers:
// Creation (valid/invalid)
// Update (role-restricted + validation)
// Delete (Admins only)
// List (search, pagination, eager loading)

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush(); // simulate fresh cache

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
        'user_id' => $this->employee->id,
    ]);
});

// ✅ Create expense (valid)
it('allows employee to create an expense', function () {
    $token = $this->employee->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/expenses', [
        'title' => 'Test Expense',
        'amount' => 100.25,
        'category' => 'Food',
    ]);

    $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Expense']);
});

// ❌ Create expense with validation error
it('rejects invalid expense creation', function () {
    $token = $this->employee->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/expenses', [
        'title' => '', // missing
        'amount' => -10,
        'category' => '',
    ]);

    $response->assertStatus(422);
});

// ✅ Manager can update expense
it('allows manager to update an expense', function () {
    $token = $this->manager->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->putJson("/api/expenses/{$this->expense->id}", [
        'title' => 'Updated Title',
        'amount' => 999,
        'category' => 'Tech',
    ]);

    $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Title']);
});

// ❌ Employee cannot update expense
it('prevents employee from updating an expense', function () {
    $token = $this->employee->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->putJson("/api/expenses/{$this->expense->id}", [
        'title' => 'Hack Attempt',
    ]);

    $response->assertStatus(403);
});

// ✅ Admin can delete
it('allows admin to delete an expense', function () {
    $token = $this->admin->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->deleteJson("/api/expenses/{$this->expense->id}");

    $response->assertStatus(200)
            ->assertJson(['message' => 'Expense deleted successfully']);
});

// ❌ Manager cannot delete
it('prevents manager from deleting an expense', function () {
    $token = $this->manager->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->deleteJson("/api/expenses/{$this->expense->id}");

    $response->assertStatus(403);
});

// ✅ List expenses with eager loading + search
it('returns a paginated list of expenses for the company with user info', function () {
    $token = $this->employee->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->getJson('/api/expenses?search=');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'amount', 'category', 'user'],
                ],
                'links',
                // 'meta',
            ]);
});
