<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
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
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        // Cache invalidation
        $this->clearExpenseCache($expense->company_id);
        Log::info('Observer: Cache INVALIDATION (created) for expense #' . $expense->id);
        
        // Audit logging
        $this->auditLogger->logAction(
            Auth::user(),
            'create',
            null,
            $expense->toArray()
        );
    }

    /**
     * Handle the Expense "updating" event.
     */
    public function updating(Expense $expense): void
    {
        // Audit logging before the update completes
        $this->auditLogger->logAction(
            Auth::user(),
            'update',
            $expense->getOriginal(),
            $expense->toArray()
        );
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Cache invalidation
        $this->clearExpenseCache($expense->company_id);
        Cache::forget('expense_' . $expense->id);
        Log::info('Observer: Cache INVALIDATION (updated) for expense #' . $expense->id);
    }

    /**
     * Handle the Expense "deleting" event.
     */
    public function deleting(Expense $expense): void
    {
        // Audit logging before the delete completes
        $this->auditLogger->logAction(
            Auth::user(),
            'delete',
            $expense->toArray(),
            null
        );
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        // Cache invalidation
        $this->clearExpenseCache($expense->company_id);
        Cache::forget('expense_' . $expense->id);
        Log::info('Observer: Cache INVALIDATION (deleted) for expense #' . $expense->id);
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        // Cache invalidation
        $this->clearExpenseCache($expense->company_id);
        Cache::forget('expense_' . $expense->id);
        Log::info('Observer: Cache INVALIDATION (restored) for expense #' . $expense->id);
        
        // Audit logging
        $this->auditLogger->logAction(
            Auth::user(),
            'restore',
            null,
            $expense->toArray()
        );
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        // Cache invalidation
        $this->clearExpenseCache($expense->company_id);
        Cache::forget('expense_' . $expense->id);
        Log::info('Observer: Cache INVALIDATION (force deleted) for expense #' . $expense->id);
    }
    
    /**
     * Helper method to clear expense caches for a company
     *
     * @param  int  $companyId
     * @return void
     */
    private function clearExpenseCache($companyId)
    {
        // Use pattern-based cache clearing for all keys starting with 'expenses_{company_id}_'
        Cache::forget('expenses_' . $companyId . '_*');
        Log::info('Observer: Cache INVALIDATION for company #' . $companyId . ' expenses');
    }
} 