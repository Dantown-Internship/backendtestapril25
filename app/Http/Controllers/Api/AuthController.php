<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class AuthController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(private UserService $userService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authorize('createCompany', User::class);

        try {
            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
            ]);

            // Create admin user
            $user = $this->userService->createUser([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'company_id' => $company->id,
                'role' => UserRole::Admin,
            ]);
            $data['company '] = $company;
            $data['user'] = $user;
            $message = 'Company registered successfully';

            return $this->successResponse($user, $message, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to register a company', 500, $e->getMessage());
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();

            $data['token'] = $user->createToken('API Token')->plainTextToken;
            $data['user'] = $user;
            $message = 'User login successfully';

            return $this->successResponse($data, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occured', 500, $e->getMessage());
        }
    }

    public function logout(): JsonResponse
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            $message = 'Logged out successfully';

            return $this->successResponse(null, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occured', 500, $e->getMessage());
        }
    }
}
