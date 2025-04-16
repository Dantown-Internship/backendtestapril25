<?php

use App\Enums\Roles;
use App\Models\Company;
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

test('admin can list users', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->getJson('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                ],
            ],
            'total',
            'next_page',
        ]);

    expect(count($response->json('data')))->toBe(3);
});

test('manager cannot list users', function () {
    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->getJson('/api/users');

    $response->assertStatus(403);
});

test('admin can create user', function () {
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'role' => Roles::EMPLOYEE->value,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->postJson('/api/users', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'role' => Roles::EMPLOYEE->value,
        'company_id' => $this->company->id,
    ]);
});

test('manager cannot create user', function () {
    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Roles::EMPLOYEE->value,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->postJson('/api/users', $userData);

    $response->assertStatus(403);
});

test('admin can update user role', function () {
    $updateData = [
        'role' => Roles::MANAGER->value,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->putJson('/api/users/'.$this->employee->id, $updateData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->employee->id,
        'role' => Roles::MANAGER->value,
    ]);
});

test('manager cannot update user role', function () {
    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $updateData = [
        'role' => Roles::ADMIN->value,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->putJson('/api/users/'.$this->employee->id, $updateData);

    $response->assertStatus(403);
});

test('admin can delete user', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$this->token,
    ])->deleteJson('/api/users/'.$this->employee->id);

    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', [
        'id' => $this->employee->id,
    ]);
});

test('manager cannot delete user', function () {
    $managerToken = $this->manager->createToken('manager-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$managerToken,
    ])->deleteJson('/api/users/'.$this->employee->id);

    $response->assertStatus(403);
    $this->assertDatabaseHas('users', [
        'id' => $this->employee->id,
    ]);
});
