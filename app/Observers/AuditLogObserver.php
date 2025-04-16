<?php

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;

class AuditLogObserver
{
    /**
     * Create a new observer instance.
     */
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->auditLogService->log('create', $model, null, $model->getAttributes());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->auditLogService->log('update', $model, $model->getOriginal(), $model->getAttributes());
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->auditLogService->log('delete', $model, $model->getOriginal(), null);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->auditLogService->logChange(
            'restore',
            $model,
            null,
            $model->getAttributes()
        );
    }
}
