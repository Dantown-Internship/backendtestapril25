<?php

use App\Enums\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('only admin can access this resource', function () {
    $employee = User::factory()->create([
        'role' => Role::Employee->value,
    ]);
    $admin = User::factory()->create([
        'role' => Role::Admin->value,
    ]);
    $manager = User::factory()->create([
        'role' => Role::Manager->value,
    ]);
    actingAs($employee)
        ->getJson(route('api.v1.users.index'))
        ->assertStatus(403);
    actingAs($manager)
        ->getJson(route('api.v1.users.index'))
        ->assertStatus(403);

    actingAs($admin)
        ->getJson(route('api.v1.users.index'))
        ->assertStatus(200);
});

test('admin can only view users in their company', function () {
    $admin = User::factory()->admin()->create()->fresh();
    $countOfUsers = 7;
    User::factory($countOfUsers)->create([
        'company_id' => $admin->company_id,
    ]);
    $response = actingAs($admin)
        ->getJson(route('api.v1.users.index'))
        ->assertJsonCount($countOfUsers + 1, 'data')
        ->assertJson([
            'meta' => [
                'pagination' => [
                    'total' => $countOfUsers + 1,
                ],
            ],
        ]);
    $uuids = collect($response->json('data'))->pluck('id');
    $companyUsersCount = User::where('company_id', $admin->company_id)->whereIn('uuid', $uuids)->count();
    expect($uuids)->toHaveCount($companyUsersCount);
});

test('filters can be applied when viewing users in company', function () {
    $admin = User::factory()->admin()->create()->fresh();
    User::factory(10)->employee()->create([
        'company_id' => $admin->company_id,
    ]);
    User::factory(5)->manager()->create([
        'company_id' => $admin->company_id,
    ]);

    $response = actingAs($admin)
        ->getJson(
            route('api.v1.users.index',
                [
                    'role' => Role::Manager->value,
                    'per_page' => 5,
                ]))
        ->assertStatus(200)
        ->assertJson([
                'meta' => [
                    'pagination' => [
                        'per_page' => 5,
                        'total' => 5,
                    ],
                ],
        ]);
    $roles = collect($response->json('data'))->pluck('role')->unique();
    expect($roles)->toHaveCount(1);
    expect($roles->first())->toBe(Role::Manager->value);
});
