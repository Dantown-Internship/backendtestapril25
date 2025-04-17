<?php

namespace App\Exceptions;

use App\Http\Controllers\Concerns\HasApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    private function handleApiException(Throwable $exception, $request)
    {
        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: 422,
                errors: $exception->validator->errors()->getMessages(),
            );
        }

        $previous = $exception->getPrevious();
        // Handle model not found exceptions
        if ($previous instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($previous->getModel()));
            return $this->errorResponse(
                message: "Unable to find {$modelName} with the specified identifier",
                statusCode: 404,
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse($exception->getMessage(), 404);
        }

        // Handle method not allowed errors
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                message: 'Method not allowed for this endpoint',
                statusCode: 405
            );
        }

        // Handle authentication exceptions
        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(message: 'Unauthenticated', statusCode: 401);
        }

        // Handle authorization exceptions
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(
                message: 'You are not authorized to perform this action',
                statusCode: 403
            );
        }

        // Handle rate limiting
        if ($exception instanceof ThrottleRequestsException) {
            return $this->errorResponse(
                message: 'Too many requests',
                statusCode: 429
            );
        }

        // Handle generic HTTP exceptions
        if ($exception instanceof HttpException) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getStatusCode()
            );
        }

        // Handle all other exceptions - only show detailed errors in debug mode
        $message = config('app.debug')
            ? $exception->getMessage()
            : 'Unexpected server error';

        return $this->errorResponse(
            message: $message,
            statusCode: 500,
            errors: config('app.debug') ? [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ] : null
        );
    }
}
