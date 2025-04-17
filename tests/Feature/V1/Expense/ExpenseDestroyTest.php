<?php

use App\DataTransferObjects\AuditLogChangesDto;
use App\Enums\AuditLogAction;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('unauthorized user cannot delete other company expense', function() {
    $user = User::factory()->admin()->create();
    $otherCompanyExpense = Expense::factory()->create();

    actingAs($user)
        ->deleteJson(route('api.v1.expenses.destroy', [$otherCompanyExpense->uuid]))
        ->assertNotFound();

    assertDatabaseHas('expenses', ['id' => $otherCompanyExpense->id]);
});


test('employee cannot delete an expense', function () {
    $user = User::factory()->employee()->create();
    $otherCompanyExpense = Expense::factory()->create(['company_id' => $user->company_id]);

    actingAs($user)
        ->deleteJson(route('api.v1.expenses.destroy', [$otherCompanyExpense->uuid]))
        ->assertForbidden();
});


test('manager cannot delete an expense', function () {
    $user = User::factory()->manager()->create();
    $otherCompanyExpense = Expense::factory()->create(['company_id' => $user->company_id]);

    actingAs($user)
        ->deleteJson(route('api.v1.expenses.destroy', [$otherCompanyExpense->uuid]))
        ->assertForbidden();
});

test('admin can delete an expense that belongs to their company', function () {

    $user = User::factory()->admin()->create();
    $expense = Expense::factory()->create(['company_id' => $user->company_id]);

    actingAs($user)
        ->deleteJson(route('api.v1.expenses.destroy', [$expense->uuid]))
        ->assertOk();

    assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

test('audit log is recorded when an expense is deleted', function () {
    $user = User::factory()->admin()->create();
    $expense = Expense::factory()->create(['company_id' => $user->company_id])->fresh();

    actingAs($user)
        ->deleteJson(route('api.v1.expenses.destroy', [$expense->uuid]))
        ->assertOk();

    expect(AuditLog::count())->toBe(1);
    assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'action' => AuditLogAction::Delete,
        'changes' => (new AuditLogChangesDto(
            old: $expense->getAttributes(),
            new: null
            ))
            ->toJson()
    ]);
});
