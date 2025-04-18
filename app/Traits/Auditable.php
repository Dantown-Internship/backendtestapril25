<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::updated(function (Model $model) {
            $changes = $model->getDirty();
            if (!empty($changes)) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'action' => 'update',
                    'changes' => [
                        'old' => array_intersect_key($model->getOriginal(), $changes),
                        'new' => $changes,
                    ],
                    'auditable_type' => get_class($model),
                    'auditable_id' => $model->id,
                ]);
            }
        });

        static::deleted(function (Model $model) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'action' => 'delete',
                'changes' => [
                    'old' => $model->getOriginal(),
                ],
                'auditable_type' => get_class($model),
                'auditable_id' => $model->id,
            ]);
        });
    }
}
