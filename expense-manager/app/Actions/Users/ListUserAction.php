<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;

class ListUserAction
{
    public function handle($search, $perPage)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Create cache key based on search and pagination
        $cacheParams = [
            'search' => $search ?? '',
            'per_page' => $perPage,
            'page' => request()->input('page', 1),
        ];

        $cacheKey = CacheService::getCompanyCacheKey('users', $cacheParams);

        // Return cached data if available
        return CacheService::remember($cacheKey, function () use ($companyId, $search, $perPage) {
            $query = User::where('company_id', $companyId);

            // Apply search filters
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('role', 'like', '%'.$search.'%');
                });
            }

            return $query->latest()->paginate($perPage);
        }, 300); // Cache for 5 minutes
    }
}
