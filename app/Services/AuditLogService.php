<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public function logAction(User $user, string $action, array $changes): AuditLog
    {
        return AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => $action,
            'changes' => $changes,
        ]);
    }

    public function getLogsForCompany(int $companyId)
    {
        return AuditLog::with(['user'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);
    }
}