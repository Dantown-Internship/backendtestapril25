<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CacheService
{
    /**
     * Get a cache key for the current user's company
     *
     * @param string $prefix
     * @param array $additionalParams
     * @return string
     */
    public static function getCompanyCacheKey(string $prefix, array $additionalParams = []): string
    {
        $user = Auth::user();
        $companyId = $user->id;

        $key = "{$prefix}:company:{$companyId}";

        if (!empty($additionalParams)) {
            $key .= ':' . implode(':', $additionalParams);
        }

        return $key;
    }

    /**
     * Get cached data or store the result of the callback
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public static function remember(string $key, callable $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget a cache key
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear all cache for a company
     *
     * @param string $prefix
     * @return bool
     */
    public static function clearCompanyCache(string $prefix): bool
    {
        $user = Auth::user();
        $companyId = $user->id;

        $pattern = "{$prefix}:company:{$companyId}:*";

        return Cache::tags([$pattern])->flush();
    }
}
