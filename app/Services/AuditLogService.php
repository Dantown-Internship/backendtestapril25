<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function log($action, $oldData = null, $newData = null)
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => $action,
            'changes' => json_encode([
                'old' => $oldData,
                'new' => $newData,
            ]),
        ]);
    }
}
