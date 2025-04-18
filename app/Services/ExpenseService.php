<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ExpenseService
{
    /**
     * Get all expenses for a company with filtering and pagination.
     *
     * @param int $companyId
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getExpenses(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;
        $perPage = $filters['per_page'] ?? 15;
        $cacheKey = "expenses:company:{$companyId}:page:{$page}:perPage:{$perPage}";
        
        if (!empty($filters['search'])) {
            $cacheKey .= ":search:" . md5($filters['search']);
        }
        
        if (!empty($filters['category'])) {
            $cacheKey .= ":category:" . $filters['category'];
        }
        
        return Cache::remember($cacheKey, 600, function () use ($companyId, $filters, $perPage) {
            $query = Expense::with('user')
                ->where('company_id', $companyId);
            
            $this->applyFilters($query, $filters);
            
            return $query->latest()->paginate($perPage);
        });
    }
    
    /**
     * Apply filters to the query builder.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters(Builder $query, array $filters = []): void
    {
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%");
            });
        }
        
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
    }
    
    /**
     * Get expenses for reporting (for the weekly job).
     *
     * @param int $companyId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getExpensesForReporting(int $companyId, string $startDate, string $endDate): Collection
    {
        return Expense::with('user')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Clear cache for a company's expenses.
     *
     * @param int $companyId
     * @return void
     */
    public function clearExpenseCache(int $companyId): void
    {
        $cacheKey = "expenses:company:{$companyId}:*";
        Cache::forget($cacheKey);
    }
}
