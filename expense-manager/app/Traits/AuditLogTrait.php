<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait AuditLogTrait
{
    function storeAudit(string $action,  array $oldData, array $newData =null)
    {
        $user = Auth::user();
        // Track if there are actual changes
        $trackedFields = ['title', 'amount', 'category'];
        $changes = [];
        if ($newData) {
            foreach ($trackedFields as $field) {
                if (
                    array_key_exists($field, $oldData) &&
                    array_key_exists($field, $newData) &&
                    $oldData[$field] != $newData[$field]
                ) {
                    $changes['old'][$field] = $oldData[$field];
                    $changes['new'][$field] = $newData[$field];
                }
            }
        }
        // create if there are any changes
        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'expense_id' => $oldData['id'] ?? null,
                'action' => $action,
                'changes' => $changes,
            ]);
        }
    }
}
