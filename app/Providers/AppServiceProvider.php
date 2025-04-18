<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // //
        $tenant = Tenant::where('domain', request()->getHost())->first();

        if ($tenant) {
            app()->instance('tenant', $tenant);
        }
    }
}
