<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function created(Expense $expense)
    {
        //
    }

    /**
     * Handle the Expense "updated" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function updated(Expense $expense)
    {
        if (Auth::check()) {
            $original = $expense->getOriginal();
            $changes = [];
            
            foreach ($original as $key => $value) {
                if ($expense->$key != $value && in_array($key, ['title', 'amount', 'category'])) {
                    $changes[$key] = [
                        'old' => $value,
                        'new' => $expense->$key
                    ];
                }
            }
            
            if (!empty($changes)) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'company_id' => $expense->company_id,
                    'action' => 'update_expense',
                    'changes' => $changes
                ]);
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function deleted(Expense $expense)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'action' => 'delete_expense',
                'changes' => $expense->toArray()
            ]);
        }
    }

    /**
     * Handle the Expense "restored" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function restored(Expense $expense)
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     *
     * @param  \App\Models\Expense  $expense
     * @return void
     */
    public function forceDeleted(Expense $expense)
    {
        //
    }
}
