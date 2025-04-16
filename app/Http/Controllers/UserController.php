<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request, UserService $userService)
    {
        Gate::authorize('viewAny', User::class);

        $users = $userService->getUsersByCompany($request->user())
            ->paginate(10);

        return successJsonResponse('Users retrieved successfully.', $users);
    }

    public function store(CreateUserRequest $request, UserService $userService)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $user = $userService->createUser($data);

        return successJsonResponse('User created successfully.', ['user' => $user]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user, UserService $userService)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        $userService->updateUser($user, $data);

        return successJsonResponse('User role updated successfully.');
    }
}
