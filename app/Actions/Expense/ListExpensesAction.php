<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class ListExpensesAction
{
    public function __construct(
        private Expense $expense
    ) {}

    public function execute(array $listExpenseRecordOptions, array $relationships = [])
    {
        $paginationPayload = $listExpenseRecordOptions['pagination_payload'] ?? null;
        $filterRecordOptionsPayload = $listExpenseRecordOptions['filter_record_options_payload'] ?? null;

        $query = $this->expense->query()
            ->with($relationships)
            ->orderBy('created_at', 'desc');

        if (!empty($filterRecordOptionsPayload['company_id'])) {
            $query->where('company_id', $filterRecordOptionsPayload['company_id']);
        }

        if (!empty($filterRecordOptionsPayload['expense_category_id'])) {
            $query->where('expense_category_id', $filterRecordOptionsPayload['expense_category_id']);
        }

        if (!empty($filterRecordOptionsPayload['search_query'])) {
            $query->where('title', 'LIKE', $filterRecordOptionsPayload['search_query'] . '%');
        }

        if ($paginationPayload) {
            $paginatedExpenses = $query->paginate(
                $paginationPayload['limit'] ?? config('businessConfig.default_page_limit'),
                ['*'],
                'page',
                $paginationPayload['page'] ?? 1
            );

            return [
                'expense_payload' => $paginatedExpenses->items(),
                'pagination_payload' => [
                    'meta' => generatePaginationMeta($paginatedExpenses),
                    'links' => generatePaginationLinks($paginatedExpenses)
                ],
            ];
        }

        $expenses = $query->get();

        return [
            'expense_payload' => $expenses,
        ];
    }
}
