<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(private UserService $userService)
    {
        
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);

            $users = $this->userService->getUsersByCompany($request->user()->company_id, $request->per_page);

            $message = $users->isEmpty() ? 'No users  found.' : 'Uusers fetched successfully';

            return $this->successResponse($users, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch users', 500, $e->getMessage());
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            $message = 'User saved successfully';

            return $this->successResponse($user, $message, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to save user', 500, $e->getMessage());
        }
    }

    public function update(int $id, UserRequest $request): JsonResponse
    {
        try {
            $this->authorize('update', Auth()->user());

            $user = $this->userService->updateUser($id, $request->validated());

            $message = 'User updated successfully';

            return $this->successResponse($user, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', 500, $e->getMessage());
        }
    }
}
