<?php

namespace App\Actions\AuditLog;

use App\Models\AuditLog;

class GetAuditLogByIdAction
{
    public function __construct(
        private AuditLog $auditLog
    )
    {
        
    }
    public function execute(string $auditLogId, array $relationships = [])
    {
        return $this->auditLog->with(
            $relationships
        )->where([
            'id' => $auditLogId
        ])->first();
    }
}