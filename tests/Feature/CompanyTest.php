<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_company_information(): void
    {
        $company = Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'company@test.com',
        ]);
        
        $user = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);
        
        $response = $this->actingAs($user)
                         ->getJson('/api/company');
                         
        $response->assertOk();
        $response->assertJsonStructure([
            'company' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
        
        $response->assertJson([
            'company' => [
                'name' => 'Test Company',
                'email' => 'company@test.com',
            ],
        ]);
    }
    
    public function test_update_company_information(): void
    {
        $company = Company::factory()->create([
            'name' => 'Old Company Name',
            'email' => 'old@test.com',
        ]);
        
        $user = User::factory()->admin()->create([
            'company_id' => $company->id,
        ]);
        
        $response = $this->actingAs($user)
                         ->putJson('/api/company', [
                             'name' => 'New Company Name',
                             'email' => 'new@test.com',
                         ]);
                         
        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'company',
        ]);
        
        $response->assertJson([
            'message' => 'Company information updated successfully',
            'company' => [
                'name' => 'New Company Name',
                'email' => 'new@test.com',
            ],
        ]);
        
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'New Company Name',
            'email' => 'new@test.com',
        ]);
    }
    
    public function test_non_admin_cannot_update_company(): void
    {
        $company = Company::factory()->create();
        
        $user = User::factory()->employee()->create([
            'company_id' => $company->id,
        ]);
        
        $response = $this->actingAs($user)
                         ->putJson('/api/company', [
                             'name' => 'New Company Name',
                             'email' => 'new@test.com',
                         ]);
                         
        $response->assertForbidden();
    }
} 