<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheHandler
{
    public function cache(string $key, \Closure $callback, int $ttl = 3600) // 3600 seconds = 1 hour
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function makeCacheKey(string $base, array $params = []): string
    {
        if (empty($params)) {
            return $base;
        }

        // Hash large/complex filters to keep key short
        return $base . ':' . md5(json_encode($params));
    }
}
