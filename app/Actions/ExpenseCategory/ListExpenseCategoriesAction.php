<?php

namespace App\Actions\ExpenseCategory;

use App\Models\ExpenseCategory;

class ListExpenseCategoriesAction
{
    public function __construct(
        private ExpenseCategory $expenseCategory
    ) {}

    public function execute(array $listExpenseCategoryRecordOptions, array $relationships = [])
    {
        $paginationPayload = $listExpenseCategoryRecordOptions['pagination_payload'] ?? null;
        $filterRecordOptionsPayload = $listExpenseCategoryRecordOptions['filter_record_options_payload'] ?? null;

        $query = $this->expenseCategory->query()
            ->with($relationships)
            ->orderBy('name', 'asc');

        if (!empty($filterRecordOptionsPayload['company_id'])) {
            $query->where('company_id', $filterRecordOptionsPayload['company_id']);
        }

        if ($paginationPayload) {
            $paginatedExpenseCategories = $query->paginate(
                $paginationPayload['limit'] ?? config('businessConfig.default_page_limit'),
                ['*'],
                'page',
                $paginationPayload['page'] ?? 1
            );

            return [
                'expense_category_payload' => $paginatedExpenseCategories->items(),
                'pagination_payload' => [
                    'meta' => generatePaginationMeta($paginatedExpenseCategories),
                    'links' => generatePaginationLinks($paginatedExpenseCategories)
                ],
            ];
        }

        $expenseCategories = $query->get();

        return [
            'expense_category_payload' => $expenseCategories,
        ];
    }
}
