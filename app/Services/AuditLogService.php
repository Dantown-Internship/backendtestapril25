<?php

namespace App\Services;

use App\Jobs\LogAuditEntryJob;
use App\Models\User;
use App\Services\Contracts\AuditLogServiceInterface;
use Illuminate\Support\Facades\Auth;

class AuditLogService implements AuditLogServiceInterface
{
    public  function log(string $action, array $oldData, array $newData = [], ?User $user = null): void
    {
        $user = $user ?: Auth::user();

        if (! $user) return;

        LogAuditEntryJob::dispatch(
            userId: $user->id,
            companyId: $user->company_id,
            action: $action,
            changes: [
                'old' => $oldData,
                'new' => $newData,
            ]
        );
    }
}
