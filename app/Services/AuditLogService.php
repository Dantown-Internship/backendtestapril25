<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log a change to a model.
     *
     * @param string $action The action performed (create, update, delete)
     * @param Model $model The model that was changed
     * @param array|null $oldValues The old values before the change
     * @param array|null $newValues The new values after the change
     * @return AuditLog
     */
    public function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        // Skip audit logging during seeding
        if (app()->runningInConsole() && !app()->environment('testing')) {
            return new AuditLog();
        }

        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'company_id' => $model->company_id ?? $user?->company_id,
            'action' => $action,
            'changes' => [
                'old' => $oldValues,
                'new' => $newValues,
            ],
            'model_type' => get_class($model),
            'model_id' => $model->id,
        ]);
    }

    /**
     * Get the changes between old and new model states.
     *
     * @param Model $model
     * @param array $oldAttributes
     * @param array $newAttributes
     * @return array
     */
    public function getChanges(Model $model, array $oldAttributes, array $newAttributes): array
    {
        $changes = [];

        foreach ($newAttributes as $key => $value) {
            if (!array_key_exists($key, $oldAttributes) || $oldAttributes[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldAttributes[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }
}
