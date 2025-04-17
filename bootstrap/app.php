<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use App\Jobs\SendExpenseReport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;



if (!function_exists('mapRoutes')) {
    function mapRoutes()
    {
        $routes = [
            'auth'     => '/../routes/auth/auth.php',
            'users'    => '/../routes/users/users.php',
            'company'  => '/../routes/company/company.php',
            'company/expense' => '/../routes/company/expense.php',
        
        ];

        foreach ($routes as $prefix => $routeFile) {
            Route::prefix($prefix === 'default' ? '' : $prefix)->group(function () use ($routeFile) {
                require __DIR__ . $routeFile;
            });
        }
    }
}


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->group(function () {
                    mapRoutes();
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e): JsonResponse {
            return dantownResponse([], 401, $e->getMessage(), false); 
        });
        $exceptions->render(function (AuthorizationException $e): JsonResponse {
            return dantownResponse([], 403, $e->getMessage(), false);   
        });

    })->withSchedule(function (Schedule $schedule){
        Log::info('Scheduler loaded: ' . now());
        $dayOfWeek = (int) env('EXPENSE_REPORT_DAY', 1);
        $time =     env('EXPENSE_REPORT_TIME');
        $schedule->job(new SendExpenseReport)->everyMinute()->timezone('Africa/Lagos');
        // $schedule->job(new SendExpenseReport)->weeklyOn($dayOfWeek, $time)->timezone('Africa/Lagos');

    })->create();
