<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your route middleware (alias => class)
        $middleware->alias([
            'company' => \App\Http\Middleware\EnsureCompanyAccess::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Add middleware to groups if needed
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(\App\Jobs\SendWeeklyExpenseReports::class)
            ->weekly()
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->name('weekly_expense_reports');
    })
    ->withExceptions(function (Exceptions $exceptions) {
           // 🔹 Always return JSON responses for API requests
           $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e) => $request->expectsJson());

           // 🔹 Handle Validation Errors (422)
           $exceptions->render(function (ValidationException $exception, Request $request) {
               return response()->json([
                   'error' => 'Validation Failed',
                   'message' => $exception->errors(),
               ], 422);
           });

           // 🔹 Handle Not Found Errors (404)
           $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
               return response()->json([
                   'error' => 'Resource Not Found',
                   'message' => 'The requested resource was not found.',
               ], 404);
           });

           // 🔹 Handle Database Errors (500)
           $exceptions->render(function (QueryException $exception, Request $request) {
               return response()->json([
                   'error' => 'Database Error',
                   'message' => $exception->getMessage(),
               ], 500);
           });

           // 🔹 Handle All Other Server Errors (500)
           $exceptions->render(function (Throwable $exception, Request $request) {
               return response()->json([
                   'error' => 'Server Error',
                   'message' => $exception->getMessage(),
               ], 500);
           });
    })->create();