<?php

namespace App\Actions\AuditLog;

use App\Models\AuditLog;

class CreateAuditLogAction
{
    public function __construct(
        private AuditLog $auditLog
    )
    {}

    public function execute(array $createAuditLogRecordOptions)
    {
        return $this->auditLog->create(
            $createAuditLogRecordOptions
        );
    }
}