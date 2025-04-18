<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $respond = function (string $message, int $code) {
            return response()->json([
                'data' => [
                    'message' => $message,
                ],
            ], $code);
        };

        $exceptions->render(function (HttpException $e, $request) use ($respond) {
            if ($e->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                return $respond("Not found", Response::HTTP_NOT_FOUND);
            }

            return $respond($e->getMessage(), Response::HTTP_NOT_FOUND);
        });
    })->create();
