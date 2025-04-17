<?php

namespace App;

use App\Models\AuditLogs;
use Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        // record create
        static::created(function($model){
            self::logActivity('created', $model, $model->getAttributes());
        });

        // record updates
        static::updated(function($model) {
            $changes = [];

            foreach($model->getDirty() as $key => $value){
                $changes[$key] = [
                    'old' => $model->getOriginal($key),
                    'new' => $value
                ];
            }

            self::logActivity('updated', $model, $changes);
        });

        // record deletes
        static::deleted(function($model) {
            self::logActivity('deleted', $model, null);
        });
    }

    protected static function logActivity($action, $model, $changes)
    {
        // get company id 
        $companyId = $model->company_id ?? auth('api')->user()->company_id ?? null;

        if(!$companyId){
            return;
        }

        AuditLogs::create([
            'user_id' => Auth::id(),
            'company_id' => $companyId,
            'action' => $action,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }
}
