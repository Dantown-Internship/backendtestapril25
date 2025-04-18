<?php

namespace App\Exception\Handlers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationExceptionHandler
{
    public function __invoke(AuthenticationException $exception, Request $request): Response
    {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }
}