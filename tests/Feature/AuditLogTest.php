<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_is_created_at_every_expense_update(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $expense = Expense::factory()->create();

        $token = $user->createToken('API Token')->plainTextToken;

        $expenseData = Expense::factory()->make()->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson(route('expenses.update', $expense->id), $expenseData);

        $response->assertStatus(200);
        $this->assertCount(1, Expense::all());
        $this->assertCount(1, AuditLog::all());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update',
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
    }

    public function test_audit_log_is_created_at_every_expense_delete(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $expense = Expense::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('expenses.delete', $expense));

        $response->assertStatus(200);
        $this->assertCount(0, Expense::all());
        $this->assertCount(1, AuditLog::all());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'delete',
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
    }
}
