<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function company_has_many_users()
    {
        $company = Company::factory()->create();
        $users = User::factory()->count(3)->create([
            'company_id' => $company->id
        ]);
        
        $this->assertCount(3, $company->users);
        $this->assertInstanceOf(User::class, $company->users->first());
    }

    /** @test */
    public function user_belongs_to_company()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $this->assertEquals($company->id, $user->company->id);
        $this->assertInstanceOf(Company::class, $user->company);
    }

    /** @test */
    public function user_has_many_expenses()
    {
        // Disable model events to prevent observers from firing
        Event::fake();
        
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        Expense::factory()->count(5)->create([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);
        
        $user = $user->fresh();
        
        $this->assertCount(5, $user->expenses);
        $this->assertInstanceOf(Expense::class, $user->expenses->first());
    }

    /** @test */
    public function expense_belongs_to_user_and_company()
    {
        // Disable model events to prevent observers from firing
        Event::fake();
        
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);
        
        $expense = $expense->fresh();
        
        $this->assertEquals($user->id, $expense->user->id);
        $this->assertEquals($company->id, $expense->company->id);
        $this->assertInstanceOf(User::class, $expense->user);
        $this->assertInstanceOf(Company::class, $expense->company);
    }

    /** @test */
    public function audit_log_belongs_to_user_and_company()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $auditLog = AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'action' => 'create',
            'changes' => ['test' => 'data']
        ]);
        
        $this->assertEquals($user->id, $auditLog->user->id);
        $this->assertEquals($company->id, $auditLog->company->id);
        $this->assertInstanceOf(User::class, $auditLog->user);
        $this->assertInstanceOf(Company::class, $auditLog->company);
    }
} 