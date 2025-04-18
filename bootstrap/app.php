<?php

use App\Http\Middleware\{AdminMiddleware, AdminOrManagerMiddleware, EnsureUserBelongsToCompany};
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your middleware alias
        $middleware->alias([
            'company.user' => EnsureUserBelongsToCompany::class,
            'admin' => AdminMiddleware::class,
            'adminOrmanager' => AdminOrManagerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
