<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * The AuditLogger service instance.
     */
    protected $auditLogger;

    /**
     * Create a new observer instance.
     */
    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->clearUserCache($user->company_id);
        Log::info('Observer: Cache INVALIDATION (created) for user #' . $user->id);
        
        $this->auditLogger->logAction(
            Auth::user() ?? $user,
            'create',
            null,
            $user->makeHidden(['password', 'remember_token'])->toArray()
        );
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        $this->auditLogger->logAction(
            Auth::user() ?? $user,
            'update',
            $user->getOriginal(null, false),
            $user->makeHidden(['password', 'remember_token'])->toArray()
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->clearUserCache($user->company_id);
        Cache::forget('user_' . $user->id);
        Log::info('Observer: Cache INVALIDATION (updated) for user #' . $user->id);
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        $this->auditLogger->logAction(
            Auth::user() ?? $user,
            'delete',
            $user->makeHidden(['password', 'remember_token'])->toArray(),
            null
        );
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->clearUserCache($user->company_id);
        Cache::forget('user_' . $user->id);
        Log::info('Observer: Cache INVALIDATION (deleted) for user #' . $user->id);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->clearUserCache($user->company_id);
        Cache::forget('user_' . $user->id);
        Log::info('Observer: Cache INVALIDATION (restored) for user #' . $user->id);
        
        $this->auditLogger->logAction(
            Auth::user() ?? $user,
            'restore',
            null,
            $user->makeHidden(['password', 'remember_token'])->toArray()
        );
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->clearUserCache($user->company_id);
        Cache::forget('user_' . $user->id);
        Log::info('Observer: Cache INVALIDATION (force deleted) for user #' . $user->id);
    }
    
    /**
     * Helper method to clear user caches for a company
     *
     * @param  int  $companyId
     * @return void
     */
    private function clearUserCache($companyId)
    {
        // Use pattern-based cache clearing for all keys starting with 'users_{company_id}_'
        Cache::forget('users_' . $companyId . '_*');
        Log::info('Observer: Cache INVALIDATION for company #' . $companyId . ' users');
    }
} 