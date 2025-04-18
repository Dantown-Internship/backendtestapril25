<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use App\Traits\ApiResponse;

class Handler extends ExceptionHandler
{
    use ApiResponse;

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
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return $this->error(
                    'Resource not found or you do not have permission to access it.',
                    404
                );
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->error(
                    'The requested endpoint was not found.',
                    404
                );
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = $e->getCode();
                $status = is_numeric($status) && $status >= 100 && $status < 600 ? $status : 500;

                return $this->error(
                    $e->getMessage() ?: 'An unexpected error occurred.',
                    $status
                );
            }
        });
    }
}
