<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Manually create an audit log entry for this model.
     *
     * @param string $action    'update' | 'delete' | other
     * @param array  $old       Old values (before change)
     * @param array  $new       New values (after change)
     * @return void
     */
    public function auditLog(string $action, array $old = [], array $new = [])
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'changes'    => [
                'old' => $old,
                'new' => $new ?: null,
            ]
        ]);
    }
}
