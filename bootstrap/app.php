<?php

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
        $middleware->alias([
            'role' => \App\Http\Middleware\AdminMiddleware::class,
        ]); // Middleware That Guides The Admin Route.
    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'userMgt' => \App\Http\Middleware\UserMiddleware::class,
        ]); // This Middleware Helps Admins To Manage Users.
    })
    
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
