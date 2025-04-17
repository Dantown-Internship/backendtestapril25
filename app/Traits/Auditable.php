<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    // Static array to store old values before update
    protected static $auditOldValues = [];
    
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $changes = [
                'new' => $model->getAttributes(),
            ];
            self::createLogEntry($model, 'created', $changes);
        });

        static::updating(function (Model $model) {
            // Store original values in a static array indexed by model ID
            static::$auditOldValues[$model->getKey()] = $model->getOriginal();
        });

        static::updated(function (Model $model) {
            $modelId = $model->getKey();
            
            // Get old values from static array
            $oldValues = static::$auditOldValues[$modelId] ?? [];
            
            $changes = [
                'old' => $oldValues,
                'new' => $model->getChanges(),
            ];
            
            self::createLogEntry($model, 'updated', $changes);
            
            // Clean up after use
            if (isset(static::$auditOldValues[$modelId])) {
                unset(static::$auditOldValues[$modelId]);
            }
        });

        static::deleted(function (Model $model) {
            $changes = [
                'old' => $model->getOriginal(),
                'new' => [], 
            ];
            self::createLogEntry($model, 'deleted', $changes);
        });
    }

    private static function createLogEntry(Model $model, string $event, array $changes)
    {
        if (!Auth::check()) {
            throw new \RuntimeException('No authenticated user for audit log');
        }

        if (empty($model->company_id)) {
            throw new \RuntimeException('Model missing company_id for audit log');
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $model->company_id,
            'action' => strtolower(class_basename($model)) . '_' . $event,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }

    private static function debugChanges(Model $model, string $event, array $changes)
    {
        if (app()->environment('local')) { // debugging locally
            dump([
                'model' => get_class($model),
                'event' => $event,
                'changes' => $changes,
                'auth_user' => Auth::check() ? Auth::id() : null,
                'company_id' => $model->company_id ?? null
            ]);
        }
    }
}