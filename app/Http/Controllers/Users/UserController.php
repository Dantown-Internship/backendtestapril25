<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use App\Services\Auth\RoleService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\User\CreateUserRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{

    public function __construct(
        public UserService $userService,
        protected RoleService $roleService
    ) {}


    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!');
            }

            $data = $request->validated();
            $roleName = $data['role_name'];
            unset($data['role_name']);

            $user = $this->userService->create($data, $roleName);

            return dantownResponse($user, 201, 'User created!', true);
        } catch (Exception $e) {
            return dantownResponse([], 500, $e->getMessage(), false);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        }
    }



    public function users()
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!');
            }
            $users = $this->userService->users();
            return dantownResponse($users, 200, 'Users retrieved!', true);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        } catch (ModelNotFoundException $e) {
            return dantownResponse([], 404, $e->getMessage(), false);
        }
    }


    public function user(string $userId)
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!');
            }
            $user = $this->userService->user($userId);
            return dantownResponse($user, 200, 'User retrieved!', true);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        } catch (ModelNotFoundException $e) {
            return dantownResponse([], 404, 'No record found!', false);
        }
    }



    public function update(string $userId, UpdateUserRequest $request): JsonResponse
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!');
            }

            $data = $request->validated();
            $user = $this->userService->update($userId, $data);

            return dantownResponse($user, 200, 'Update successful!', true);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        } catch (ModelNotFoundException $e) {
            return dantownResponse([], 404, 'No record found!', false);
        }
    }


    public function delete(string $userId)
    {
        try {
            if (!$this->roleService->userHasRole(auth()->user(), 'admin')) {
                throw new AuthorizationException('Only Admin authorized action!', 403);
            }
            $user = $this->userService->delete($userId);
            return dantownResponse($user, 204, 'Resource deleted!', true);
        } catch (ModelNotFoundException $e) {
            return dantownResponse([], 404, "No record found!", false);
        }
    }
}
