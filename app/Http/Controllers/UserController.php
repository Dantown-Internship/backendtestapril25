<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\CacheHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use CacheHandler;

    public function index(Request $request, UserService $userService)
    {
        Gate::authorize('viewAny', User::class);

        $user = $request->user();
        $baseKey = 'users:company:' . $user->company_id;
        $cacheKey = $this->makeCacheKey($baseKey, []);

        $users = $this->cache($cacheKey, function () use ($userService, $user) {
            return $userService->getUsersByCompany($user)->paginate(10);
        });

        return successJsonResponse('Users retrieved successfully.', $users);
    }

    public function store(CreateUserRequest $request, UserService $userService)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $admin = $request->user();
        $user = $userService->createUser($admin, $data);

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
