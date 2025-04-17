<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class GetWeeklyExpensesAction
{
    public function __construct(
        private Expense $expense
    ) {}

    public function execute(array $GetWeeklyExpensesAction, array $relationships = [])
    {
        $companyId = $GetWeeklyExpensesAction['company_id'] ?? null;

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return $this->expense->with($relationships)->when($companyId, function ($model, $companyId) {
            $model->where('company_id', $companyId);
        })->whereBetween('created_at', [$startOfWeek, $endOfWeek])->orderBy('created_at', 'asc')->get();
    }
}
