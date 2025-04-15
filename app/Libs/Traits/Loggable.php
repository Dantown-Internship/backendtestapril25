<?php

namespace App\Libs\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\Activity;


trait Loggable
{
    protected static function bootLoggable()
    {

        $class = new static();

        $events = $class->getLoggableAttributes();

        if (count($events) > 0) {
            foreach ($events as $event) {
                static::{$event}(fn($model) => $model->logActivity($event));
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

        // Get the fields to log from the model's $loggableFields property
        $loggableFields = property_exists($this, 'loggableFields') ? $this->loggableFields : [];

        if ($action === 'deleted') {
            // For deletions, log the old values of the loggable fields
            $changes = [
                'old' => $this->only($loggableFields),
                'new' => null,
            ];
        } elseif ($action === 'updated') {
            // For updates, log the old and new values of the loggable fields
            $dirtyFields = array_intersect_key($this->getDirty(), array_flip($loggableFields));
            $changes = [
                'old' => array_intersect_key($this->getOriginal(), $dirtyFields),
                'new' => $dirtyFields,
            ];
        }

        // Create an activity log entry
        $activity = new Activity([
            'company_id' => App::make('currentCompany')->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => json_encode($changes),
        ]);

        $activity->save();
    }
}
