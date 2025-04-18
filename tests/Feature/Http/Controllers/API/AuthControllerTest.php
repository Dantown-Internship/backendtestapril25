<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $validPassword;
    private $invalidPassword;
    protected function setUp(): void
    {
        parent::setUp();

        $this->validPassword = $this->faker->password(10);
        $this->invalidPassword = $this->faker->password(3, 6);
    }

    public function test_it_can_register_new_user(): void
    {
        $payload = [
            "name" => $this->faker()->name(),
            "email" => $this->faker()->email(),
            "password" => $this->validPassword,
            "password_confirmation" => $this->validPassword,
            "company" => [
                "name" => $this->faker()->company(),
                "email" => $this->faker()->email(),
            ]
        ];

        $response = $this->postJson(route("auth.register"), $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                "data" => [
                    "user",
                    "token",
                ]
            ]);

        $this->assertDatabaseHas(app(Company::class)->getTable(), $payload["company"]);
        unset($payload["company"], $payload["password"], $payload["password_confirmation"]);
        $this->assertDatabaseHas(app(User::class)->getTable(), $payload);
    }


    public function test_it_throws_validation_error_when_user_passwords_do_not_match(): void
    {
        $payload = [
            "name" => $this->faker()->name(),
            "email" => $this->faker()->email(),
            "password" => $this->validPassword,
            "password_confirmation" => $this->invalidPassword,
            "company" => [
                "name" => $this->faker()->company(),
                "email" => $this->faker()->email(),
            ]
        ];

        $response = $this->postJson(route("auth.register"), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                "message",
                "errors" => [
                    "password",
                ]
            ]);

        $this->assertDatabaseMissing(app(Company::class)->getTable(), $payload["company"]);
        unset($payload["company"], $payload["password"], $payload["password_confirmation"]);
        $this->assertDatabaseMissing(app(User::class)->getTable(), $payload);
    }


    public function test_it_allows_user_with_valid_password_login(): void
    {
        $user = User::factory()->create();

        $payload = [
            "email" => $user->email,
            "password" => "password",
        ];

        $response = $this->postJson(route("auth.login"), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "data" => [
                    "user",
                    "token",
                ]
            ]);
    }


    public function test_it_does_not_allow_user_with_invalid_password_to_login(): void
    {
        $user = User::factory()->create();

        $payload = [
            "email" => $user->email,
            "password" => "invalidPassword",
        ];

        $response = $this->postJson(route("auth.login"), $payload);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                "data" => [
                    "message" => __('exceptions.user.invalid_login')
                ]
            ]);
    }


    public function test_it_deletes_user_token_upon_logout(): void
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => "password",
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'auth_token',
            'last_used_at' => null
        ]);

        $this->assertGuest();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $response['data']['token'],
            'X-Requested-With' => 'XMLHttpRequest'
        ])
            ->postJson(route('auth.logout'))
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'auth_token',
            'last_used_at' => null
        ]);
    }
}
