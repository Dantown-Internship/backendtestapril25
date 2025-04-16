<?php

namespace App\Actions\Expenses;

use App\Enums\Roles;
use App\Models\Expense;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListExpensesAction
{
    public function handle(array $filters, int $perPage): LengthAwarePaginator
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Create cache key based on filters and pagination
        $cacheParams = [
            'search' => $filters['search'] ?? '',
            'category' => $filters['category'] ?? '',
            'per_page' => $perPage,
            'page' => request()->input('page', 1),
        ];

        $cacheKey = CacheService::getCompanyCacheKey('expenses', $cacheParams);

        // Return cached data if available
        return CacheService::remember($cacheKey, function () use ($user, $companyId, $filters, $perPage) {
            $query = Expense::with('user')->where('company_id', $companyId);

            // Show only employee expenses
            if ($user->role === Roles::EMPLOYEE) {
                $query->where('user_id', $user->id);
            }

            // Apply search filters
            if (! empty($filters['search'])) {
                $query->where(function ($q) use ($filters): void {
                    $q->where('title', 'like', '%'.$filters['search'].'%')
                        ->orWhere('category', 'like', '%'.$filters['search'].'%');
                });
            }

            // Apply category filter
            if (! empty($filters['category'])) {
                $query->where('category', $filters['category']);
            }

            return $query->latest()->paginate($perPage);
        }, 300); // Cache for 5 minutes
    }
}
