<?php

use App\DataTransferObjects\AuditLogChangesDto;
use App\Enums\AuditLogAction;
use App\Enums\ExpenseCategory;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

test('employees cannot update an expense', function () {
    $employee = User::factory()->employee()->create();
    $expense = Expense::factory()
        ->create([
            'user_id' => $employee->id,
            'company_id' => $employee->company_id,
        ])->fresh();

    actingAs($employee)
        ->putJson(
            route('api.v1.expenses.update', $expense->uuid),
            [
                'title' => 'random',
                'amount' => 100,
                'category' => ExpenseCategory::Food->value,
            ])
        ->assertForbidden();
});

test('admins can update an expense', function () {
    $admin = User::factory()->admin()->create();
    $expense = Expense::factory()->create(['company_id' => $admin->company_id]);
    $updatePayload = [
        'title' => 'New title',
        'amount' => 200,
        'category' => ExpenseCategory::Food->value,
    ];

    actingAs($admin)
        ->putJson(
            route('api.v1.expenses.update', $expense->uuid),
            $updatePayload
        )
        ->assertStatus(200)
        ->assertJson([
            'data' => array_merge($updatePayload, ['id' => $expense->uuid]),
        ]);
});

test('managers can update an expense', function () {
    $manager = User::factory()->manager()->create();
    $expense = Expense::factory()->create(['company_id' => $manager->company_id]);
    $updatePayload = [
        'title' => 'New title',
        'amount' => 200,
        'category' => ExpenseCategory::Food->value,
    ];

    actingAs($manager)
        ->putJson(
            route('api.v1.expenses.update', $expense->uuid),
            $updatePayload
        )
        ->assertStatus(200)
        ->assertJson([
            'data' => array_merge($updatePayload, ['id' => $expense->uuid]),
        ]);
});

test('validation error is thrown when invalid input is passed', function () {
    $admin = User::factory()->admin()->create();
    $expense = Expense::factory()->create(['company_id' => $admin->company_id]);
    $updatePayload = [
        'title' => 10,
        'amount' => 'test',
        'category' => 'random category',
    ];
    actingAs($admin)
        ->putJson(
            route('api.v1.expenses.update', $expense->uuid),
            $updatePayload
        )
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['amount', 'title', 'category']);
});

test('audit log is created when an expense is created', function () {
    $admin = User::factory()->admin()->create();
    $expense = Expense::factory()->create(['company_id' => $admin->company_id])->fresh();
    $updatePayload = [
        'title' => 'New title',
        'amount' => 200,
        'category' => ExpenseCategory::Food->value,
    ];

    actingAs($admin)
        ->putJson(
            route('api.v1.expenses.update', $expense->uuid),
            $updatePayload
        )
        ->assertStatus(200)
        ->assertJson([
            'data' => array_merge($updatePayload, ['id' => $expense->uuid]),
        ]);

    expect(AuditLog::count())->toBe(1);
    assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'company_id' => $admin->company_id,
        'action' => AuditLogAction::Update,
        'changes' => (new AuditLogChangesDto(
            old: $expense->getRawOriginal(),
            new: $expense->fresh()->getAttributes())
            )
                ->toJson(),
    ]);
});
