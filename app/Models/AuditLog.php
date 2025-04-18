<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public static function logAudit(string $action, array $oldData = [], $newData = null)
    {
        $user = auth()->user();

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => $action,
            'changes' => json_encode([
                'old' => $oldData,
                'new' => $newData ? $newData->toArray() : null,
            ]),
        ]);
    }
}
