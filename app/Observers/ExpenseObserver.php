<?php

namespace App\Observers;

use App\Actions\ActivityLoggerAction;
use App\Models\Expense;
use App\Models\Log;

class ExpenseObserver
{
    public function __construct(private ActivityLoggerAction $logger) {
    }

    public function updating(Expense $expense) {
        $this->logger->log(
            request()->method(), 
            $expense,  
            ['changes' => $expense->getOriginal()]
        );
    }

    public function deleting(Expense $expense) {
        $this->logger->log(
            request()->method(), 
            $expense,  
        );
    }
}
