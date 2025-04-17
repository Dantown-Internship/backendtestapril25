<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_register_successfully()
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value
        ]);
        $this->actingAs($admin);

        $registrationData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'role' => RoleEnum::ADMIN->value,
            'company_id' => $admin->company_id,
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'user'
                     ],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'company_id' => $registrationData['company_id'],
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_fails_with_invalid_data()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $registrationData = [
            'name' => '', 
            'email' => 'not-an-email', 
            'password' => 'short',
            'role' => 'invalid-role',
            'company_id' => $admin->company_id,
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name',
                     'email',
                     'password',
                     'role',
                 ]);
    }

    public function test_unauthorized_user_cannot_register()
    {
        $this->withExceptionHandling();

        $registrationData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'company_id' => (string) Str::uuid(),
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(401);
    }
}
