<?php

namespace App\Libs\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Activity;


trait Loggable
{
    protected static function bootLoggable()
    {
        $class = new static();

        $events = $class->getLoggableAttributes();

        if(count($events) > 0){
            foreach($events as $event){
                static::{$event}(fn ($model) => $model->logActivity($event));
            }
            
        }
        
    }

    public function getLoggableAttributes(): array
    {
        return $this->loggable ?? [];
    }

    public function logActivity($action)
    {
        $changes = [];
        
        if ($action === 'updated') {
            $changes = [
                'old' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                'new' => $this->getDirty()
            ];
        }

        Activity::create([
            'company_id' => $this->company_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => json_encode($changes)
        ]);
    }
}
