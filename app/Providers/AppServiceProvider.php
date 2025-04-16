<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Observers\AuditLogObserver;
use App\Observers\ExpenseObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\AuditLogService::class);
        $this->app->singleton(\App\Services\ExpenseReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Company::observe(AuditLogObserver::class);
        User::observe(AuditLogObserver::class);
        Expense::observe(AuditLogObserver::class);
        Expense::observe(ExpenseObserver::class);

        // Configure API rate limiting using database driver
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by(optional($request->user())->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many attempts, please try again later.',
                        'retry_after' => $headers['Retry-After'],
                    ], 429);
                });
        });
    }
}
