<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class Controller
{
    public function customJsonResponse($data = null, $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['data' => $data], $status);
    }
}
