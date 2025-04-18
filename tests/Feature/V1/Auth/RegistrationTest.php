<?php

use App\Models\Company;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

test('new company and admin is created during registration', function () {
    $payload = [
        'name' => 'New Admin',
        'email' => 'newadmin@mail.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Test Company',
        'company_email' => 'companyemail@mail.com',
    ];
    $response = postJson(route('api.v1.register'), $payload);
    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                    'company' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'token' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ],
        ])
        ->assertJson([
            'data' => [
                'user' => [
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'role' => 'admin',
                    'company' => [
                        'name' => $payload['company_name'],
                        'email' => $payload['company_email'],
                    ],
                ],
            ],
        ]);

    $userData = $response->json()['data']['user'];
    assertDatabaseHas('users', [
        'name' => $userData['name'],
        'email' => $userData['email'],
        'role' => $userData['role'],
    ]);
    assertDatabaseHas('companies', [
        'name' => $userData['company']['name'],
        'email' => $userData['company']['email'],
    ]);
});

test('validation error is thrown when invalid input is given', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id]);
    $payload = [
        'name' => '',
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'not-matching',
        'company_name' => '',
        'company_email' => $company->email,
    ];
    postJson(route('api.v1.register'), $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'company_name', 'company_email']);
});
