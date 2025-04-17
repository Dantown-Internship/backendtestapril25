<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, UserService $userService)
    {
        $data = $request->validated();

        $data['role'] = 'Admin';

        $newUser = $userService->createUser($request->user(), $data);

        return successJsonResponse('Admin registered successfully.', $newUser, 201);
    }

    public function login(LoginRequest $request, UserService $userService)
    {
        $data = $request->validated();

        $user = $userService->getUserByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return errorJsonResponse('The provided credentials are incorrect.', null, 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return successJsonResponse('User logged in.', ['access_token' => $token, 'user' => $user]);
    }
}
