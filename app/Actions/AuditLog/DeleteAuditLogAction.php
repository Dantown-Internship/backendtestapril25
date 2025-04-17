<?php

namespace App\Actions\AuditLog;

use App\Models\AuditLog;

class DeleteAuditLogAction
{
    public function __construct(
        private AuditLog $auditLog
    )
    {
        
    }
    public function execute(string $auditLogId)
    {
        return $this->auditLog->where([
            'id' => $auditLogId
        ])->delete();
    }
}