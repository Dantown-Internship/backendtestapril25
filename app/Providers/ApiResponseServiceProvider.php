<?php

namespace App\Providers;


use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public function boot(): void
    {
        Response::macro('apiSuccess', function ($data = null, string $message = 'Request successful', int $statusCode = 200) {
            if ($data instanceof LengthAwarePaginator) {
                return response()->json([
                    'data'        => $data->items(),
                    'pagination'  => [
                        'total'        => $data->total(),
                        'per_page'     => $data->perPage(),
                        'current_page' => $data->currentPage(),
                        'last_page'    => $data->lastPage(),
                    ],
                    'status'      => 'success',
                    'message'     => $message,
                    'api_version' => '1.0'
                ], $statusCode);
            }

            return response()->json([
                'data'        => $data,
                'status'      => 'success',
                'message'     => $message,
                'api_version' => '1.0'
            ], $statusCode);
        });


        Response::macro('apiError', function (string $errorType, string $message = 'An error occurred', int $statusCode = 400, $log = false) {
            if ($log) {
                Log::error("[{$errorType}] {$message}");
            }

            return response()->json([
                'error' => [
                    'type'    => $errorType,
                    'message' => $message,
                    'status'  => 'failed'
                ]
            ], $statusCode);
        });

        Response::macro('apiValidationError', function (Validator $validator, string $message = 'The given data was invalid.') {
            return response()->json([
                'errors'  => $validator->errors(),
                'status'  => 'failed',
                'message' => $message,
            ], 422);
        });

        Response::macro('apiCreated', function ($data = null, string $message = 'Resource created') {
            return response()->apiSuccess($data, $message, 201);
        });

    }
}
