<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogAuditEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $companyId,
        public string $action,
        public array $changes,
    ) {}

    public function handle(): void
    {
        AuditLog::create([
            'user_id'    => $this->userId,
            'company_id' => $this->companyId,
            'action'     => $this->action,
            'changes'    => $this->changes,
        ]);
    }
}
