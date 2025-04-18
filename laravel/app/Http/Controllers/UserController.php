<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getUsersByCompanyId(auth()->user()->company_id);

        return response()->json($users);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userService->createUser($validated);

        return $user;
    }

    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userService->updateUser($id, $validated);

        return $user;
    }
}
