<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    //Json Response global method
    /**
     * @param string|array $message
     * @param array|null $meta
     * @param int $statusCode
     */
    public function respones(string | array $message, ?array $meta = null, int $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'meta' => $meta,
        ], $statusCode);
    }
    public function formatError($validator)
    {
        $errors = collect($validator->errors()->toArray())->flatten()->toArray();
        return $errors;
    }
}