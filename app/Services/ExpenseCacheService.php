<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ExpenseCacheService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'expenses:';

    public function getCachedExpenses(int $companyId, array $filters = []): array
    {
        $cacheKey = $this->generateCacheKey($companyId, $filters);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($companyId, $filters) {
            $query = Expense::where('company_id', $companyId)
                ->with(['user:id,name', 'company:id,name']);

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', "%{$filters['search']}%")
                      ->orWhere('category', 'like', "%{$filters['search']}%");
                });
            }

            if (!empty($filters['sort_by_amount'])) {
                $query->orderBy('amount', $filters['sort_by_amount']);
            }

            return $query->paginate(10)->toArray();
        });
    }

    public function invalidateCompanyExpenses(int $companyId): void
    {
        $pattern = self::CACHE_PREFIX . "company:{$companyId}:*";
        $keys = Redis::keys($pattern);
        
        if (!empty($keys)) {
            Redis::del($keys);
        }
    }

    private function generateCacheKey(int $companyId, array $filters): string
    {
        $key = self::CACHE_PREFIX . "company:{$companyId}";
        
        if (!empty($filters['search'])) {
            $key .= ":search:{$filters['search']}";
        }
        
        if (!empty($filters['sort_by_amount'])) {
            $key .= ":sort:{$filters['sort_by_amount']}";
        }
        
        return $key;
    }
} 