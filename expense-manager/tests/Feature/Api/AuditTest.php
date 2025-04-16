<?php

use App\Enums\Roles;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
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

test('audit log is created when expense is updated', function () {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    $updateData = [
        'title' => 'Updated Expense',
        'amount' => 200.75,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->putJson('/api/expenses/'.$expense->id, $updateData);

    $response->assertStatus(200);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $this->admin->id,
        'company_id' => $this->company->id,
        'action' => 'update',
    ]);
});

test('audit log is created when expense is deleted', function () {
    $expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->employee->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->deleteJson('/api/expenses/'.$expense->id);

    $response->assertStatus(204);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $this->admin->id,
        'company_id' => $this->company->id,
        'action' => 'delete',
    ]);
});

test('admin can view audit logs', function () {
    AuditLog::factory()->count(3)->create([
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/audits');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'success',
            'total',
        ]);

    expect(count($response->json('data')))->toBe(3);
});

test('manager cannot view audit logs', function () {
    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->getJson('/api/audits');

    $response->assertStatus(403);
});

test('employee cannot view audit logs', function () {
    $employeeToken = $this->employee->createToken('employee-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$employeeToken,
    ])->getJson('/api/audits');

    $response->assertStatus(403);
});

test('admin cannot view audit logs from other company', function () {
    $otherCompany = Company::factory()->create();
    $otherAuditLog = AuditLog::factory()->create([
        'company_id' => $otherCompany->id,
        'user_id' => $this->admin->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/audits/'.$otherAuditLog->id);

    $response->assertStatus(404);
});
