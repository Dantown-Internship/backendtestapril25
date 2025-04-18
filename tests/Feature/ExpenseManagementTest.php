<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable all model observers for testing
        \App\Models\User::unsetEventDispatcher();
        \App\Models\Expense::unsetEventDispatcher();
        \App\Models\Company::unsetEventDispatcher();
    }

    /** @test */
    public function employees_can_view_expenses_from_their_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $user = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'Employee'
        ]);
        
        // Create expenses for user's company
        Expense::factory()->count(5)->create([
            'company_id' => $company1->id,
            'user_id' => $user->id
        ]);
        
        // Create expenses for different company
        Expense::factory()->count(3)->create([
            'company_id' => $company2->id
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson('/api/expenses');
        
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }
    
    /** @test */
    public function employees_can_create_expenses()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/expenses', [
            'title' => 'Office Supplies',
            'amount' => 150.75,
            'category' => 'Supplies'
        ]);
        
        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Office Supplies',
                'amount' => 150.75,
                'category' => 'Supplies',
                'user_id' => $user->id,
                'company_id' => $company->id
            ]);
            
        $this->assertDatabaseHas('expenses', [
            'title' => 'Office Supplies',
            'amount' => 150.75,
            'category' => 'Supplies',
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);
    }
    
    /** @test */
    public function managers_can_update_expenses()
    {
        $company = Company::factory()->create();
        $employee = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Manager'
        ]);
        
        $expense = Expense::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'title' => 'Old Title',
            'amount' => 100,
            'category' => 'Old Category'
        ]);
        
        Sanctum::actingAs($manager);
        
        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Title',
            'amount' => 200,
            'category' => 'Updated Category'
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated Title',
            'amount' => 200,
            'category' => 'Updated Category'
        ]);
    }
    
    /** @test */
    public function employees_cannot_update_expenses()
    {
        $company = Company::factory()->create();
        $employee1 = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        $employee2 = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Employee'
        ]);
        
        $expense = Expense::factory()->create([
            'company_id' => $company->id,
            'user_id' => $employee2->id
        ]);
        
        Sanctum::actingAs($employee1);
        
        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Updated Title',
            'amount' => 200,
            'category' => 'Updated Category'
        ]);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function admins_can_delete_expenses()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        
        $expense = Expense::factory()->create([
            'company_id' => $company->id
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->deleteJson("/api/expenses/{$expense->id}");
        
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id
        ]);
    }
    
    /** @test */
    public function managers_cannot_delete_expenses()
    {
        $company = Company::factory()->create();
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'Manager'
        ]);
        
        $expense = Expense::factory()->create([
            'company_id' => $company->id
        ]);
        
        Sanctum::actingAs($manager);
        
        $response = $this->deleteJson("/api/expenses/{$expense->id}");
        
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id
        ]);
    }
    
    /** @test */
    public function cannot_access_expenses_from_different_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        $admin = User::factory()->create([
            'company_id' => $company1->id,
            'role' => 'Admin'
        ]);
        
        $expense = Expense::factory()->create([
            'company_id' => $company2->id
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->getJson("/api/expenses/{$expense->id}");
        
        $response->assertStatus(403);
    }
} 