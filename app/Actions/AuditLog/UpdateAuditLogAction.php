<?php

namespace App\Actions\AuditLog;

use App\Models\AuditLog;

class UpdateAuditLogAction
{
    public function __construct(
        private AuditLog $auditLog
    )
    {
        
    }
    public function execute(array $updateAuditLogRecordOptions)
    {
        $auditLogId = $updateAuditLogRecordOptions['id'];
        $data = $updateAuditLogRecordOptions['data'];

        return $this->auditLog->where([
            'id' => $auditLogId
        ])->update($data);
    }
}