<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logCreation();
        });

        static::updated(function ($model) {
            $model->logUpdate();
        });

        static::deleted(function ($model) {
            $model->logDeletion();
        });
    }

    protected function getCurrentUserId()
    {
        // Check if we're running in console (artisan command)
        if (app()->runningInConsole()) {
            // You might want to return a system user ID or null
            return null;
        }

        return Auth::check() ? Auth::id() : null;
    }

    protected function getCurrentCompanyId()
    {
        $user = Auth::user();

        if ($user) {
            return $user->company_id;
        }

        // If there's a company_id on the model itself
        if (isset($this->company_id)) {
            return $this->company_id;
        }

        return null;
    }

    protected function getIpAddress()
    {
        if (app()->runningInConsole()) {
            return 'console';
        }

        return Request::ip();
    }

    public function logCreation()
    {
        $userId = $this->getCurrentUserId();
        if (is_null($userId)) return;

        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $this->getCurrentCompanyId(),
            'action' => 'create',
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'changes' => $this->toArray(),
            'ip_address' => $this->getIpAddress(),
        ]);
    }

    public function logUpdate()
    {
        $userId = $this->getCurrentUserId();
        if (is_null($userId)) return;

        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $this->getCurrentCompanyId(),
            'action' => 'update',
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'changes' => [
                'old' => $this->getOriginal(),
                'new' => $this->toArray(),
            ],
            'ip_address' => $this->getIpAddress(),
        ]);
    }

    public function logDeletion()
    {
        $userId = $this->getCurrentUserId();
        if (is_null($userId)) return;

        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $this->getCurrentCompanyId(),
            'action' => 'delete',
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'changes' => $this->toArray(),
            'ip_address' => $this->getIpAddress(),
        ]);
    }
}