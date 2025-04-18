<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DataTransferObjects\TokenDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('Login successful', [
            'user' => new UserResource($user->load('company')),
            'token' => (new TokenDto($token))->toArray()
        ]);
    }

}
