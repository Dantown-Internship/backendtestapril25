<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_only_admin_can_create_user(): void
    {
        $roleAllowed = ['Admin'];
        $rolesNotAllowed = ['Manager', 'Employee'];

        foreach (array_merge($roleAllowed, $rolesNotAllowed) as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);
        }

        $this->actingAs($user);

        $payload = [
            'name' => 'Micheal King',
            'email' => 'mking@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('store.user'), $payload);

        if (in_array($role, $roleAllowed)) {
            $response->assertStatus(201);

            $this->assertDatabaseHas('users', [
                'email' => 'mking@example.com',
            ]);

            $response->dump();
        } else {
            $response->assertForbidden();
        }
    }

    public function test_only_admin_can_get_users(): void
    {
        $roleAllowed = ['Admin'];
        $rolesNotAllowed = ['Manager', 'Employee'];

        foreach (array_merge($roleAllowed, $rolesNotAllowed) as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);
        }

        $this->actingAs($user);

        $payload = [
            'name' => 'Micheal King',
            'email' => 'mking@example.com',
            'password' => 'password123',
        ];

        $response =$this->postJson(route('store.user'), $payload);

        if (in_array($role, $roleAllowed)) {
            $response = $this->getJson(route('get.users'));
            $response->assertStatus(200);
            $response->dump();
        }else{
            $response->assertForbidden();
        }
    }

    public function test_only_admin_can_update_user(): void
{
    $roleAllowed = ['Admin'];
    $rolesNotAllowed = ['Manager', 'Employee'];
    
    $company = Company::factory()->create();
    $companyId = $company->id;
    
    foreach (array_merge($roleAllowed, $rolesNotAllowed) as $role) {
        $actingUser = User::factory()->create([
            'role' => $role,
            'company_id' => $companyId,
        ]);
        
        $this->actingAs($actingUser);
        
        $targetUser = User::factory()->create([
            'company_id' => $companyId,
            'role' => 'Employee',
        ]);
        
        $updatePayload = [
            'name' => 'Craig David',
            'role' => 'Manager'
        ];
        
        $response = $this->putJson(route('update.user', ['id' => $targetUser->id]), $updatePayload);
        
        if (in_array($role, $roleAllowed)) {
            $response->assertStatus(200);
            $this->assertDatabaseHas('users', [
                'id' => $targetUser->id,
                'name' => 'Craig David',
                'role' => 'Manager'
            ]);
        } else {
            $response->assertForbidden();
            $this->assertDatabaseMissing('users', [
                'id' => $targetUser->id,
                'name' => 'Craig David',
                'role' => 'Manager'
            ]);
        }
        
        // Clean up to prevent interference between test iterations
        User::where('id', '!=', $actingUser->id)->delete();
    }
}
}
