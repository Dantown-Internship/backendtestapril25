<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\SignInRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use App\Services\Auth\RoleService;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService,
        protected RoleService $roleService
    ){}


    public function signup(SignupRequest $request): JsonResponse
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!');
            }

            $data = $request->validated();
            $roleName = $data['role_name'];
            unset($data['role_name']);

            $user = $this->authService->signup($data, $roleName);

            return dantownResponse($user, 201, 'User created!', true);
        } catch (Exception $e) {
            return dantownResponse([], 500, $e->getMessage(), false);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        }
    }


    public function signin(SignInRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->signin($request->validated());
            return dantownResponse($response['data'], 200,$response['message'], true);

        } catch (AuthenticationException $e) {
            return dantownResponse(null,401, $e->getMessage(),false);
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return dantownResponse(null, 200, 'Logged out sucessful!!', true);
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->me();
        return dantownResponse($user, 200, 'User retrieved successfully!', true);
    }
}
