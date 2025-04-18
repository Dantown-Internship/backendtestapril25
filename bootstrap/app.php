<?php

use App\Http\Middleware\EnsureCompanyMatch;
use App\Http\Middleware\EnsureJsonRequest;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(EnsureJsonRequest::class);
        $middleware->alias([
            'ensure.company.match' => EnsureCompanyMatch::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:prune-tmp-storage')->daily();
        $schedule->command('app:send-expense-weekly-report')->runInBackground()->weeklyOn(1, '8:00');
        $schedule->command('queue:work --max-time=275')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
