<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    public $user;
    public $company;

use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'password' => Hash::make('password123'),

        ]);

        $this->user->assignRole(RoleEnum::ADMIN);
        Sanctum::actingAs($this->user, ['*']);
    }



    /** @test */
    public function it_registers_an_admin_and_company_successfully()
    {
        $payload = [
            'company_name' => "test",
            'email' => "test@gmail.com",
            'full_name' => $this->user->name,
            'password' => $this->user->password,

        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully.',
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => "test",
            'email' => "test@gmail.com",
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);

        // check that the user has the admin role
        $user = User::where('email', $this->user->email)->first();
        $this->assertTrue($user->hasRole(RoleEnum::ADMIN));
    }

    /** @test */
    public function it_logs_in_a_user_successfully()
    {

        $payload = [
            'email' => $this->user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'user',
                'token',
                'message',
            ]);
    }

    /** @test */
    public function login_fails_with_wrong_password()
    {


        $payload = [
            'email' => $this->user->email,
            'password' => 'wrong_password',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'invalid password',
            ]);

    }
    /** @test */
    public function logs_out_user()
    {


        $this->actingAs($this->user);
        $response = $this->postJson('/api/logout');
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                  'message' => 'Successfully logged out',
                'data' => [],
            ]);

    }
}
