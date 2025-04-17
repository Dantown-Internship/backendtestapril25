<?php

use App\Models\Expense;
use App\Models\User;

use Illuminate\Support\Str;
use function Pest\Laravel\actingAs;

test('employees can only view their expenses', function () {
    $employee = User::factory()->employee()->create()->fresh();
    $expenses = Expense::factory(10)->create([
        'user_id' => $employee->id,
        'company_id' => $employee->company_id,
    ]);
    $anotherEmployee = User::factory()->employee()
        ->create(['company_id' => $employee->company_id])
        ->fresh();
    Expense::factory(5)->create([
        'user_id' => $anotherEmployee->id,
        'company_id' => $anotherEmployee->company_id,
    ]);

    actingAs($employee)
        ->getJson(route('api.v1.expenses.index'))
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => $expenses->count()
                ]
            ]
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                [
                    'id',
                    'title',
                    'amount',
                    'category',
                    'updated_at',
                ],
            ],
        ]);
});

test('managers and admins can view all the expenses in the company', function () {
    $manager = User::factory()->manager()->create();
    $expenses = Expense::factory(20)->create([
        'company_id' => $manager->company_id,
    ]);
    $admin = User::factory()->admin()->create(['company_id' => $manager->company_id]);

    actingAs($manager)
        ->getJson(route('api.v1.expenses.index'))
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => $expenses->count()
                ],
            ],
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                [
                    'id',
                    'title',
                    'amount',
                    'category',
                    'created_at',
                    'updated_at',
                    'user',
                ]
            ],
            'meta' => [
                'pagination'
            ],
        ]);

    actingAs($admin)
        ->getJson(route('api.v1.expenses.index'))
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => $expenses->count(),
                ],
            ],
        ]);
});

test('list of expenses can be filtered', function () {
    $admin = User::factory()->admin()->create();
    Expense::factory(20)->create([
        'company_id' => $admin->company_id,
    ]);
    $randomString = Str::random(10);
    $uniqueExpense = Expense::factory()->create([
        'title' => $randomString,
        'company_id' => $admin->company_id,
    ]);
    $filterPayload = [
        'per_page' => 15,
        'search' => $randomString
    ];

    $response = actingAs($admin)
        ->getJson(route('api.v1.expenses.index', $filterPayload))
        ->assertStatus(200);

    $expensesFromResponse = collect($response->json('data'));
    expect($expensesFromResponse)->toHaveCount(1);
    expect($expensesFromResponse->first()['id'])->toBe($uniqueExpense->uuid);
});

test('users cannot view expenses of other companies', function () {
    $admin = User::factory()->admin()->create();
    Expense::factory(20)->create();
    actingAs($admin)
        ->getJson(route('api.v1.expenses.index'))
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => 0,
                ],
            ],
        ]);
});
