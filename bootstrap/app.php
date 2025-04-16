<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/apis/v1.php', // Add this line for api versioning
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // app('Illuminate\Contracts\Debug\ExceptionHandler')->renderable(function (NotFoundHttpException $e, Request $request) {
        //     if (str_starts_with($request->getPathInfo(), '/api/')) {
        //         return response()->json([
        //             'error' => 'Resource not found'
        //         ], 404);
        //     }

        //     return response()->view('errors.404', [], 404);
        // });
    })->create();