<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\AuditLog;
use App\Http\Requests\Expense\CreateRequest;

use Illuminate\Support\Facades\Cache;

class ExpenseRepository
{
    public function getCompanyExpenses(int $companyId, string $search = null, int $perPage = 10)
    {
        $cacheKey = "company_expense_{$companyId}_" . md5($search ?? '');
        $cacheListKey = "company_expense_keys_{$companyId}";

        $existingKeys = Cache::get($cacheListKey, []);
        if (!in_array($cacheKey, $existingKeys)) {
            $existingKeys[] = $cacheKey;
            Cache::put($cacheListKey, $existingKeys, now()->addHours(2));
        }

        return Cache::remember($cacheKey, now()->addHour(), function() use ($companyId, $search, $perPage) {
            $query = Expense::where('company_id', $companyId)
                ->with(['user:id,name', 'company:id,name']);
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            }
            
            return $query->paginate($perPage);
        });
    }

    public function createExpense(CreateRequest $request)
    {
        $data = [
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'company_id' => $request->user()->company_id
        ];

        $expense = Expense::create($data);
        $this->clearCompanyCache($data['company_id']);
        return $expense;
    }

    public function updateExpense(Expense $expense, array $data)
    {
        AuditLog::create([
            'user_id' => auth()->user()->id,
            'company_id' => auth()->user()->company_id,
            'action' => 'update',
            'changes' => json_encode([ 
                'old' => $expense->toArray(),
                'new' => $data
            ]),
        ]);
        $expense->update($data);
        $this->clearCompanyCache($expense->company_id);
        return $expense;
    }

    public function deleteExpense(Expense $expense)
    {
        $companyId = $expense->company_id;
        AuditLog::create([
            'user_id' => auth()->user()->id,
            'company_id' => auth()->user()->company_id,
            'action' => 'delete',
            'changes' => json_encode([ 
                'old' => $expense->toArray(),
                'new' => null
            ]),
        ]);
        $expense->delete();
        $this->clearCompanyCache($companyId);
    }

public function clearCompanyCache(int $companyId)
{
    $cacheListKey = "company_expense_keys_{$companyId}";
    $keys = Cache::get($cacheListKey, []);

    foreach ($keys as $key) {
        Cache::forget($key);
    }

    Cache::forget($cacheListKey);
}
}