<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Support\Str;

class ExceptionHandler
{
    use ApiResponse;

    public function handle(Throwable $exception, Request $request)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($exception);
        }
    }

    /**
     * Handles exceptions specifically for API requests, returning a JSON response.
     *
     * @param  Throwable  $exception
     */
    protected function handleApiException( $exception): JsonResponse
    {
        $defaultMessage = 'An unexpected error occurred. Try again';

        $previous = $exception->getPrevious();

        if ($exception instanceof HttpException) {
            if ($previous instanceof ModelNotFoundException) {
                $model = $previous->getModel();
                $modelname = class_basename($model);
                return $this->notFoundResponse("$modelname not found.");
            }

            if ($previous instanceof ThrottleRequestsException) {
                return $this->errorResponse('Too Many Requests.', 429);
            }
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());

        }
        if ($exception instanceof ValidationException) {
            return $this->errorResponse(
                $exception->getMessage(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $exception->errors()
            );
        }
         if ($exception instanceof AuthenticationException) {

            return $this->errorResponse('Authentication required. Login to continue', 401);

        }
        if ($exception instanceof AuthorizationException) {
            return $this->forbiddenResponse('This action is unauthorized.');
        }

        // Catch-all for everything else
        Log::error($exception);
        return $this->errorResponse(
            app()->environment('local') ? $exception->getMessage() : $defaultMessage,
            $exception->getCode() ?: 500
        );

    }
}
