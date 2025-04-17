<?php

namespace App\Services\Management;

use App\Models\Management\Expenses;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

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
            return Expenses::with('company')
                ->when(!empty($filters['title']), fn($q) =>
                    $q->where('title', 'like', '%' . $filters['title'] . '%')
                )
                ->when(!empty($filters['company']), fn($q) =>
                    $q->whereHas('company', fn($q2) =>
                        $q2->where('name', 'like', '%' . $filters['company'] . '%')
                    )
                )
                ->latest()
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




    public function delete(string $expenseId)
    {
        $expense = Expenses::where('id', $expenseId)->first();
        if (!$expense) {
            return false;
        }
        $data = $expense->only(['title', 'category', 'amount']);
        logAudit(
            userId: $expense->user_id,
            companyId: $expense->company_id,
            action: 'delete_expense',
            changes: ['deleted' => $data]
        );

        return $expense->delete();
    }


    protected function generateCacheKey(array $filters, int $perPage): string
    {
        return 'expenses_' . md5(json_encode($filters) . "_$perPage");
    }
}
