<?php

use App\Exceptions\InvalidRequestException;
use App\Http\Helpers\ApiResponse;
use App\Http\Helpers\Constant;
use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check-role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(Constant::RESOURCE_NOT_FOUND, 'Endpoint not found.', 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(Constant::RESOURCE_NOT_FOUND, 'The requested resource was not found.', 404);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return  ApiResponse::error(Constant::VALIDATION_ERROR, $e->getMessage(), 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return  ApiResponse::error(Constant::AUTHENTICATION_ERROR, 'Unauthenticated.', 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return  ApiResponse::error(Constant::AUTHORIZATION_ERROR, 'You are not authorized to access this resource.', 403);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return  ApiResponse::error(Constant::RATE_LIMIT_ERROR, 'Too many requests. Please try again later.', 429);
            }
        });

        $exceptions->render(function (InvalidRequestException $e, Request $request) {
            if ($request->is('api/*')) {
                return  $e->render();
            }
        });

    })->create();
