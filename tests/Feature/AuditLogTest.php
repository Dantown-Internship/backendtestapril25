<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed company and user
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => 1,
        ]);

        Sanctum::actingAs($this->admin);
    }

    public function test_audit_log_created_on_expense_update()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->admin->id,
            'title' => 'Lunch',
            'amount' => 2500.00,
        ]);

        $response = $this->putJson("/api/expenses/{$expense->id}", [
            'title' => 'Business Lunch',
            'amount' => 3000.00,
            'category' => 'Meals',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'company_id' => $this->admin->company_id,
            'action' => 'update',
        ]);
    }

    public function test_audit_log_created_on_expense_delete()
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->admin->company_id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(204);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'company_id' => $this->admin->company_id,
            'action' => 'delete',
        ]);
    }
}

