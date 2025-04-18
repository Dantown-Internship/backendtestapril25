<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCreateUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    use ResponseTrait;

    public function index(): JsonResponse
    {
        try {
            $users = Cache::remember('users', 60, function () {
                return User::query()
                    ->with(['company', 'expenses'])
                    ->paginate(20);
            });

            if (! $users) {
                return $this->badRequestResponse('Could not retrieve users');
            }

            return $this->successResponse('Users retrieved successfully', ['users' => $users]);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }

    public function store(AdminCreateUserRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());

            if (! $user) {
                return $this->badRequestResponse('Could not create user');
            }

            return $this->successResponse('User created successfully', ['user' => $user->load('company')], 201);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }

    public function update(int $id, UpdateUserRoleRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $user = User::find($id);

            if (! $user) {
                return $this->notFoundResponse('Could not retrieve user');
            }

            $user->role = $requestData['role'];
            $user->save();

            return $this->successResponse('User role updated successfully', ['user' => $user->fresh()]);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }
}
