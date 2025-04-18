<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public function logAudit($user, $action, $oldData, $newData = null)
    {
        $changes = [
            'old' => $oldData,
            'new' => $newData,
        ];

        AuditLog::create([
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
            'action'     => $action,
            'changes'    => $changes,
        ]);
    }
}
