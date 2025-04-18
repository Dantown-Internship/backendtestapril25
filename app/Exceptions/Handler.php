<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
            $message = $exception->getMessage() ?: 'Server Error';

            if ($exception instanceof AuthenticationException) {
                $statusCode = 401;
                $message = 'Unauthenticated';
            }

            return new JsonResponse(
                ['error' => $message],
                $statusCode
            );
        }

        return parent::render($request, $exception);
    }
}