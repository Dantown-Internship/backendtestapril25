<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_creation(): void
    {
        $company = Company::factory()->create();

        $response = $this->postJson('/api/v1/register',[
            'firstname' => 'Jason',
            'lastname' => 'Cundy',
            'company_id' => $company->id,
            'email' => 'jason.cundy@gmail.com',
            'password' => 'password',
            'role' => 'Admin'
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'status' => 'success'
        ]);
    }
}
