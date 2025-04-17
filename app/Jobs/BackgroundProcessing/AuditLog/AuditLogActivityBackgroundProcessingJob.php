<?php

namespace App\Jobs\BackgroundProcessing\AuditLog;

use App\Actions\AuditLog\CreateAuditLogAction;
use App\Actions\User\GetUserByIdAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AuditLogActivityBackgroundProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $auditLogActivityBackgroundProcessingJob)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->auditLogActivityBackgroundProcessingJob['user_id'];
        $action = $this->auditLogActivityBackgroundProcessingJob['action'];
        $changes = $this->auditLogActivityBackgroundProcessingJob['changes'] ?? null;

        $getUserByIdAction = app(GetUserByIdAction::class);

        $user = $getUserByIdAction->execute($userId);

        if (is_null($user)) {
            return;
        }

        $createAuditLogAction = app(CreateAuditLogAction::class);

        $createAuditLogAction->execute([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => $action,
            'changes' => json_encode($changes ?? []),
        ]);
    }
}
