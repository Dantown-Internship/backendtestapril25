<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_allows_admins_retrieve_all_users(): void
    {
        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create(['role' => RoleEnum::ADMIN()]);
        $users = User::factory(20)->for($company)->create();

        $response = $this->actingAs($admin)->getJson(route("users.index"));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'url',
                            'label',
                            'active',
                        ],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_it_allows_only_admins_to_create_users(): void
    {
        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create(['role' => RoleEnum::ADMIN()]);

        $payload = [
            "name" => fake()->name(),
            "email" => fake()->email(),
            "role" => RoleEnum::MANAGER(),
        ];

        $response = $this->actingAs($admin)->postJson(route("users.store"), $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ]
            ]);
        $this->assertDatabaseHas(app(User::class)->getTable(), $payload);
    }

    public function test_it_allows_admins_to_update_users(): void
    {
        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create(['role' => RoleEnum::ADMIN()]);
        $user = User::factory()->for($company)->create();

        $payload = [
            "name" => fake()->name(),
            "email" => fake()->email()
        ];

        $response = $this->actingAs($admin)->patchJson(route("users.update", $user), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ]
            ]);
        $this->assertDatabaseHas(app(User::class)->getTable(), $payload);
    }
}
