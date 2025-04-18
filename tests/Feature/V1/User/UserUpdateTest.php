<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can update the details of a user in their company', function () {
    $admin = User::factory()->admin()->create()->fresh();
    $user = User::factory()->create(['company_id' => $admin->company_id])->fresh();
    $updatePayload = [
        'name' => 'Updated name',
        'email' => 'updated@email.com',
        'role' => Role::Employee->value,
    ];
    actingAs($admin)
        ->putJson(
            route('api.v1.users.update', [$user->uuid]),
            $updatePayload
        )
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->uuid,
                'name' => $updatePayload['name'],
                'email' => $updatePayload['email'],
                'role' => $updatePayload['role'],
            ],
        ]);
});

test('validation errors are thrown when invalid updating input is provided', function () {

    $admin = User::factory()->admin()->create()->fresh();
    $user = User::factory()->create(['company_id' => $admin->company_id])->fresh();
    $updatePayload = [
        'name' => '',
        'email' => 'invalid-email',
        'role' => 'invalid-role',
    ];

    actingAs($admin)
        ->putJson(route('api.v1.users.update', [$user->uuid]), $updatePayload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'role']);
});
