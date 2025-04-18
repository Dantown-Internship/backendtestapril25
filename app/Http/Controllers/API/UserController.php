<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $filters = [
            'per_page' => $request->get('per_page', 15),
        ];

        $users = $this->userService->getUsers(
            $request->user()->company_id,
            $filters
        );

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'data' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $updatedUser,
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
