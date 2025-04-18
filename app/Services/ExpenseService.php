<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ExpenseService
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {}



    public function getExpensesForCompany(int $companyId, array $filters = [])
    {
        // Normalize and trim filters
        $search = isset($filters['search']) ? trim($filters['search']) : null;
        $perPage = isset($filters['per_page']) ? (int)$filters['per_page'] : 15;

        // Build cache key
        $cacheKey = "company:{$companyId}:expenses:" . md5(json_encode(['search' => $search, 'per_page' => $perPage]));

        return Cache::remember($cacheKey, now()->addHour(), function () use ($perPage, $companyId, $search) {
            $query = Expense::with(['user'])->where('company_id', $companyId);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            return $query->paginate($perPage);
        });
    }

    public function generateReport(int $companyId, array $filters = [])
    {
        $query = Expense::with('user')
            ->where('company_id', $companyId);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get();
    }





    public function createExpense(array $data): Expense
    {
        $user = auth()->user();
        $data['company_id'] = $user->company_id;
        $data['user_id'] = $user->id;

        return Expense::create($data);
    }

    public function updateExpense(array $data, Expense $expense): Expense
    {
        $expense->update($data);

        // Clear cache for this company's expenses
        Redis::del("company:{$expense->company_id}:expenses:*");

        $changes = [
            'old' => $expense->toArray(),
            'new' => $data,
        ];

        $this->auditLogService->logAction(
            Auth()->user(),
            'update',
            $changes
        );

        return $expense;
    }

    public function find(int $id): Expense
    {
        $expense = Expense::findOrFail($id);

        return $expense;
    }

    public function deleteExpense(Expense $expense): void
    {
        $expense->delete();
        $this->auditLogService->logAction(
            Auth()->user(),
            'delete',
            ['old' => $expense->toArray()]
        );

        // Clear cache for this company's expenses
        Redis::del("company:{$expense->companyId}:expenses:*");
    }
}
