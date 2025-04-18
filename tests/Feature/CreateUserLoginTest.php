<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserLoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_successful_login(): void
    {
        $company = Company::factory()->create();
        User::factory()->create([
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'company_id' => $company->id
        ]);

        $response = $this->postJson('/api/v1/login',[
            'email' => 'jane.doe@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token','message','status'])
            ->assertJson(['status' => 'success','message' => 'Token created']);

    }

    public function test_unsuccessful_login()
    {
        $company = Company::factory()->create();
        User::factory()->create([
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'company_id' => $company->id
        ]);

        $response = $this->postJson('/api/login',[
            'email' => 'jane.doe@gmail.com',
            'password' => 'passwordMan'
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_login_validation_failure()
    {
        $credentials = [
            'email' => 'me',
            'password' => 'admin'
        ];

        $response = $this->postJson('api/login',$credentials);

        $response->assertStatus(422);
    }
}
