<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_create_actions()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $logger = new AuditLogger();
        
        $newValues = [
            'title' => 'New Expense',
            'amount' => 150.75,
            'category' => 'Office Supplies'
        ];
        
        $auditLog = $logger->logAction($user, 'create', null, $newValues);
        
        $this->assertInstanceOf(AuditLog::class, $auditLog);
        $this->assertEquals($user->id, $auditLog->user_id);
        $this->assertEquals($company->id, $auditLog->company_id);
        $this->assertEquals('create', $auditLog->action);
        $this->assertEquals('create', $auditLog->changes['type']);
        $this->assertEquals($newValues, $auditLog->changes['new']);
    }

    /** @test */
    public function it_logs_update_actions_with_changes()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $logger = new AuditLogger();
        
        $oldValues = [
            'title' => 'Old Title',
            'amount' => 100,
            'category' => 'Old Category'
        ];
        
        $newValues = [
            'title' => 'New Title',
            'amount' => 150,
            'category' => 'Old Category' // Unchanged
        ];
        
        $auditLog = $logger->logAction($user, 'update', $oldValues, $newValues);
        
        $this->assertInstanceOf(AuditLog::class, $auditLog);
        $this->assertEquals('update', $auditLog->action);
        $this->assertEquals('update', $auditLog->changes['type']);
        
        // Only changed fields should be in the diff
        $this->assertArrayHasKey('title', $auditLog->changes['changes']);
        $this->assertArrayHasKey('amount', $auditLog->changes['changes']);
        $this->assertArrayNotHasKey('category', $auditLog->changes['changes']);
        
        $this->assertEquals('Old Title', $auditLog->changes['changes']['title']['old']);
        $this->assertEquals('New Title', $auditLog->changes['changes']['title']['new']);
    }

    /** @test */
    public function it_logs_delete_actions()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id
        ]);
        
        $logger = new AuditLogger();
        
        $oldValues = [
            'id' => 1,
            'title' => 'Deleted Expense',
            'amount' => 200,
            'category' => 'Deleted Category'
        ];
        
        $auditLog = $logger->logAction($user, 'delete', $oldValues, null);
        
        $this->assertInstanceOf(AuditLog::class, $auditLog);
        $this->assertEquals('delete', $auditLog->action);
        $this->assertEquals('delete', $auditLog->changes['type']);
        $this->assertEquals($oldValues, $auditLog->changes['old']);
    }
} 