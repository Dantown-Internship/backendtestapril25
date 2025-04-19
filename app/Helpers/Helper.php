<?php

use App\Models\AuditLog;

if (! function_exists('log_audit_trail')) {
    function log_audit_trail($model, string $action, string $event)
    {
        $user = auth()->user();

        AuditLog::create([
            'user_id'    => $user?->id,
            'company_id' => $user?->company_id,
            'action'     => $event,
            'changes'    => json_encode([
                'old' => $model->getOriginal(),
                'new' => $action === 'updated' ? $model->getChanges() : null,
            ]),
        ]);
    }
}
