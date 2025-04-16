<?php

namespace App\Models\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip if running in console (e.g., seeder, artisan)
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();  // // Only apply if a user is authenticated

        if ($user && $user->company_id) {
            $builder->where('company_id', $user->company_id);
        }
    }
}
