<?php

use App\Models\Expense;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('employee can view their own expenses', function () {
    $employee = User::factory()->employee()->create();
    $expense = Expense::factory()->create([
        'company_id' => $employee->company_id,
        'user_id' => $employee->id
    ])->fresh();

    actingAs($employee)
        ->getJson(route('api.v1.expenses.show', $expense->uuid))
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'title',
                'amount',
                'category',
                'created_at',
                'updated_at',
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => $expense->uuid,
                'title' => $expense->title,
                'amount' => $expense->amount,
                'category' => $expense->category->value,
                'created_at' => $expense->created_at->toISOString(),
                'updated_at' => $expense->updated_at->toISOString(),
            ]
        ]);
});

test('employee can not view expenses that do not belong to them', function () {
    $employee = User::factory()->employee()->create();
    $expense = Expense::factory()->create([
        'company_id' => $employee->company_id
    ]);

    actingAs($employee)
        ->getJson(route('api.v1.expenses.show', $expense->uuid))
        ->assertNotFound();
});

test('admin and managers can view any expense that belongs to the company', function () {
    $admin = User::factory()->admin()->create();
    $expense = Expense::factory()->create([
        'company_id' => $admin->company_id,
    ])->fresh();

    $this->actingAs($admin)
        ->getJson(route('api.v1.expenses.show', $expense->uuid))
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'title',
                'amount',
                'category',
                'created_at',
                'updated_at',
                'user',
            ],
        ])
        ->assertJson([
            'data' => [
                'id' => $expense->uuid,
                'title' => $expense->title,
                'amount' => $expense->amount,
                'category' => $expense->category->value,
                'created_at' => $expense->created_at->toISOString(),
                'updated_at' => $expense->updated_at->toISOString(),
            ],
        ]);

    $manager = User::factory()->manager()->create(['company_id'  => $admin->company_id]);

    actingAs($manager)
        ->getJson(route('api.v1.expenses.show', $expense->uuid))
        ->assertOk()
        ->assertJson([
            'data' => [
                'id' => $expense->uuid,
                'title' => $expense->title,
                'amount' => $expense->amount,
                'category' => $expense->category->value,
                'created_at' => $expense->created_at->toISOString(),
                'updated_at' => $expense->updated_at->toISOString(),
            ]
        ]);
});

test('viewing invalid expense id returns 404', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)
    ->getJson(route('api.v1.expenses.show', ["dkdkdkdk"]))
    ->assertNotFound();
});

