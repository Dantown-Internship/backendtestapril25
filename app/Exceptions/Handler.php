<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    // The list of exception types that are not reported
    protected $dontReport = [
        //
    ];

    // The list of inputs that are never flashed for validation exceptions
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Exception $e) {
            //
        });
    }

    /**
     * Customize the error response for HTTP exceptions
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            // Return the 404 page
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof HttpException && $exception->getStatusCode() == 500) {
            // Return the 500 page
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }
}