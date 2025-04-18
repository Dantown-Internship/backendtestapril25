<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_audit_logs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'create',
            'changes' => json_encode(['title' => 'New Entry'])
        ]);

        $response = $this->getJson('/api/audit-logs');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'=>['data' => [['action', 'changes']]]]);
    }
}
