<?php

namespace App\Services\Management;

use App\Models\Management\Expenses;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use App\Queries\ExpenseQuery;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{

    public function create(array $expenseData): Expenses
    {
        $expense = Expenses::create($expenseData);

        logAudit(
            userId: $expense->user_id,
            companyId: $expense->company_id,
            action: 'create_expense',
            changes: ['created' => $expense->only(['title', 'category', 'amount'])]
        );

        return $expense;
    }


    public function expenses(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey($filters, $perPage);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $perPage) {
            return (new ExpenseQuery($filters))
                ->applyFilters()
                ->orderByLatest()
                ->paginate($perPage);
        });
    }
    
   
    

    public function update(string $expenseId, array $data): Expenses|bool
    {
        $expense = Expenses::where('id', $expenseId)->first();
        if (!$expense) {
            return false;
        }
        $oldData = $expense->only(['title', 'category', 'amount']);

        $expense->update($data);
        $newData = $expense->only(['title', 'category', 'amount']);

        logAudit(
            userId: $expense->user_id,
            companyId: $expense->company_id,
            action: 'update_expense',
            changes: ['old' => $oldData, 'new' => $newData]
        );

        return $expense;
    }




    public function delete(string $expenseId): bool
    {
        $expense = Expenses::where('id', $expenseId)->first();
        if (!$expense) {
            return false;
        }
        $data = $expense->only(['title', 'category', 'amount']);

        logAudit(
            userId:    $expense->user_id,
            companyId: $expense->company_id,
            action:  'delete_expense',
            changes: ['deleted' => $data]
        );

        return (new ExpenseQuery())->delete($expenseId);
    }


    protected function generateCacheKey(array $filters, int $perPage): string
    {
        $userId = Auth::id() ?? 'guest';
        $filterHash = $this->hashFilters($filters);
        return "expenses_{$userId}_{$filterHash}_{$perPage}";
    }

    protected function hashFilters(array $filters): string
    {
        $normalized = [
            'title' => $filters['title'] ?? '',
            'company' => $filters['company'] ?? '',
        ];
        return hash('sha256', json_encode($normalized));
    }

}
