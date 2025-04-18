<?php
// ✅ Feature Test 5: Audit Logging (Updates & Deletes)
// This test will check if the audit log is created correctly when an expense is updated or deleted.

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Models\AuditLog;
use App\Enums\Roles;

beforeEach(function () {
    $this->company = Company::factory()->create();

    $this->admin = User::factory()->create([
        'role' => Roles::ADMIN,
        'company_id' => $this->company->id,
    ]);

    $this->expense = Expense::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->admin->id,
        'title' => 'Office Supplies',
        'amount' => 99.99,
        'category' => 'Office',
    ]);

    DB::table('audit_logs')->truncate(); // ✅ Clear old logs
});

// ✅ Update creates audit log with old and new values
it('logs an audit entry when an expense is updated', function () {
    $token = $this->admin->createToken('auth')->plainTextToken;

    $this->withToken($token)->putJson("/api/expenses/{$this->expense->id}", [
        'title' => 'Updated Title',
        'amount' => 149.99,
        'category' => 'Supplies',
    ])->assertStatus(200);

    $log = AuditLog::latest()->first();

    echo "this much is ok";

    expect($log)->not->toBeNull();
    expect($log->action)->toBe('expense_updated');
    expect($log->user_id)->toBe($this->admin->id);
    expect($log->company_id)->toBe($this->company->id);
    expect($log->changes)->toMatchArray([
        'title' => [
            'old' => 'Office Supplies',
            'new' => 'Updated Title',
        ],
        'amount' => [
            'old' => 99.99,
            'new' => 149.99,
        ],
        'category' => [
            'old' => 'Office',
            'new' => 'Supplies',
        ],
    ]);

});



// ✅ Delete creates audit log entry
it('logs an audit entry when an expense is deleted', function () {

    $token = $this->admin->createToken('auth')->plainTextToken;

    $this->withToken($token)->deleteJson("/api/expenses/{$this->expense->id}")
        ->assertStatus(200);

    $log = AuditLog::latest()->first()->fresh();

    expect($log)->not->toBeNull();
    expect($log->action)->toBe('expense_deleted');
    expect($log->changes)->toBeNull();
});