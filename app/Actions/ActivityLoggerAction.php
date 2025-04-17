<?php

namespace App\Actions;
use App\Models\Log;
use Illuminate\Database\Eloquent\Model;

class ActivityLoggerAction {

    public function log(string $action, Model $model, array $extra = []): void
    {
        Log::create([
            'action' => $action,
            'company_id' => $model->company_id,
            'user_id' => $model->user_id,
            'changes' => $extra['changes'] ?? null,
            'created_at' => now(),
        ]);
    }
}