<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Authentication
 * 
 * Auth Related Apis
 */
class AuthController extends Controller
{
    private $authService;
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Register User
     * @unauthenticated
     * This endpoint registers a new user as admin and their company
     *
     */
    public function register(RegisterUserRequest $request)
    {
        ['user' => $user, 'token' => $token] = $this->authService->register($request->validated());

        return $this->customJsonResponse([
            'user'  => UserResource::make($user),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    /**
     * Login User
     * @unauthenticated
     * This endpoint logs in a user
     *
     */
    public function login(LoginRequest $request)
    {
        ['user' => $user, 'token' => $token] =
            $this->authService->login($request->email, $request->password);

        if (! $user) {
            return $this->customJsonResponse(
                ['message' => __('exceptions.user.invalid_login')],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->customJsonResponse([
            'user'  => UserResource::make($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout
     * 
     * This endpoint logs out a user
     *
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return $this->customJsonResponse(["message" => "Log out Successful"]);
    }
}
