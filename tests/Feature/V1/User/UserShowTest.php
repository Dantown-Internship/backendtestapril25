<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can view details of users in the company', function () {
    $admin = User::factory()->admin()->create()->fresh();
    $user = User::factory()->create([
        'company_id' => $admin->company_id,
    ])->fresh();

    actingAs($admin)
        ->getJson(route('api.v1.users.show', [$user->uuid]))
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->uuid,
                'email' => $user->email,
                'role' => $user->role->value,
                'name' => $user->name,
            ],
        ]);
});

test('admin cannot view detail of users that are not in their company', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    actingAs($admin)
        ->getJson(route('api.v1.users.show', [$user->uuid]))
        ->assertStatus(404)
        ->assertJson([
            'status' => false,
        ]);
});
