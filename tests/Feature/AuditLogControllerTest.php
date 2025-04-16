<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $admin;
    protected $manager;
    protected $employee;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company
        $this->company = Company::factory()->create();

        // Create users with different roles
        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Admin'
        ]);

        $this->manager = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Manager'
        ]);

        $this->employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'Employee'
        ]);

        // Generate token for the admin
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function admin_can_view_audit_logs()
    {
        // Create some audit logs
        AuditLog::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/audit-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'company_id',
                        'action',
                        'changes',
                        'model_type',
                        'model_id',
                        'created_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function manager_can_view_audit_logs()
    {
        $managerToken = $this->manager->createToken('test-token')->plainTextToken;

        // Create some audit logs
        AuditLog::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->manager->id
        ]);

        $response = $this->withToken($managerToken)
            ->getJson('/api/audit-logs');

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_audit_logs()
    {
        $employeeToken = $this->employee->createToken('test-token')->plainTextToken;

        $response = $this->withToken($employeeToken)
            ->getJson('/api/audit-logs');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_specific_audit_log()
    {
        $auditLog = AuditLog::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->admin->id
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/audit-logs/{$auditLog->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'company_id',
                    'action',
                    'changes',
                    'model_type',
                    'model_id',
                    'created_at',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    /** @test */
    public function cannot_view_audit_log_from_other_company()
    {
        $otherCompany = Company::factory()->create();
        $auditLog = AuditLog::factory()->create([
            'company_id' => $otherCompany->id
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/audit-logs/{$auditLog->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_clear_audit_logs_cache()
    {
        $response = $this->withToken($this->token)
            ->postJson("/api/companies/{$this->company->id}/clear-audit-logs-cache");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Audit logs cache cleared successfully'
            ]);
    }

    /** @test */
    public function non_admin_cannot_clear_audit_logs_cache()
    {
        $managerToken = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withToken($managerToken)
            ->postJson("/api/companies/{$this->company->id}/clear-audit-logs-cache");

        $response->assertStatus(403);
    }
}
