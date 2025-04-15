<?php

namespace App\Http\Controllers\Api;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;
    /**
     * List All Users
     */
    function index(Request $request, ListUserAction $action)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);

        $paginated = $action->handle($search, $perPage);
        $items = UserResource::collection($paginated->items());

        return $this->paginatedResponse('Expenses fetched successfully', $items, $paginated);
    }

    /**
     * Add User
     */
    function store(CreateUserRequest $request, CreateUserAction $action)
    {
        $user = $action->handle($request->validated());

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User created successfully'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     *  Update User Role
     */
    function update(UpdateUserRequest $request, string $id, UpdateUserAction $action)
    {
        $user = $action->handle($id, $request->validated());

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User updated successfully'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Delete User
     *
     */

    function destroy(DeleteUserAction $action, string $id)
    {
        $action->handle($id);

        return $this->noContentResponse();
    }
}
