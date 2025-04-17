<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class CreateExpenseAction
{
    public function __construct(
        private Expense $expense
    )
    {}

    public function execute(array $createExpenseRecordOptions)
    {
        return $this->expense->create(
            $createExpenseRecordOptions
        );
    }
}