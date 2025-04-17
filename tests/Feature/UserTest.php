<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public $user;
    public $company;


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
    public function it_can_add_a_user()
    {

        $this->actingAs($this->user);


        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company_id' => (string) $this->company->id,
            'password' => 'password123',
            'role' => 'EMPLOYEE',
        ];


        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => true,
            'message' => 'User created successfully.',
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'name',
                'email',
                'company_id',
                'id',
                'created_at',
                'updated_at',
                'role',
            ],
        ]);


        // Ensure that the user is saved in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

   /** @test */
public function it_can_update_user_role()
{
    $this->actingAs($this->user);

    $createData = [
        'name' => 'John Doe',
        'email' => 'john2@example.com',
        'company_id' => (string) $this->company->id,
        'password' => 'password123',
        'role' => 'EMPLOYEE',
    ];

    $createResponse = $this->postJson('/api/users', $createData);

    $createResponse->assertStatus(201);
    $createResponse->assertJson([
        'status' => true,
        'message' => 'User created successfully.',
    ]);

    $userId = $createResponse['data']['id'];


    $updateData = [
        'role' => 'MANAGER',
    ];

    $updateResponse = $this->putJson("/api/users/{$userId}", $updateData);

    $updateResponse->assertStatus(201);
    $updateResponse->assertJson([
        'status' => true,
        'message' => 'User updated successfully.',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $userId,
        'role' => 'Manager',
    ]);
}






}
