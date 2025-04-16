<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::updated(function (Model $model) {
            $changes = $model->getDirty();
            $original = $model->getOriginal();

            if (!empty($changes)) {
                $changesData = [];
                foreach ($changes as $key => $value) {
                    $changesData[$key] = [
                        'old' => $original[$key] ?? null,
                        'new' => $value,
                    ];
                }

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'company_id' => $model->company_id,
                    'action' => 'update',
                    'changes' => $changesData,
                    'model_type' => get_class($model),
                    'model_id' => $model->id,
                ]);
            }
        });

        static::deleted(function (Model $model) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'company_id' => $model->company_id,
                'action' => 'delete',
                'changes' => $model->getOriginal(),
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);
        });
    }
} 