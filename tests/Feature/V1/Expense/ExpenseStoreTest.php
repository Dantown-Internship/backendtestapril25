<?php

use App\Enums\ExpenseCategory;
use App\Models\Expense;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('users can create an expense', function () {
    $user = User::factory()->create()->refresh();
    $payload = [
        'title' => 'Random Title',
        'amount' => 700,
        'category' => ExpenseCategory::Food->value,
    ];

    $response = actingAs($user)
        ->postJson(route('api.v1.expenses.store'), $payload)
        ->assertStatus(201)
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
            ],
        ])
        ->assertJson([
            'data' => $payload,
        ]);

    $uuid = $response->json('data')['id'];
    $expense = Expense::where('uuid', $uuid)->first();
    expect($expense->company_id)->toBe($user->company_id);
});

test('validation error is thrown when invalid input is passed', function () {
    $user = User::factory()->create()->refresh();
    $payload = [
        'title' => '',
        'amount' => -100,
        'category' => 'invalid_category',
    ];

    actingAs($user)
        ->postJson(route('api.v1.expenses.store'), $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            'errors' => [
                'title',
                'amount',
                'category',
            ],
        ])
        ->assertJsonValidationErrors(['title', 'amount', 'category']);
});
