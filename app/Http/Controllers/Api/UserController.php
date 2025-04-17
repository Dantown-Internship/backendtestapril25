<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListUsersRequest $request)
    {
        $perPage = $request->validated('per_page', 10) ?? 10;
        $search = $request->validated('search');
        $users = User::when(
            ! blank($search),
            fn (Builder $builder) => $builder->where('name', 'LIKE', "%$search%"))
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginatedResponse(
            message: 'Users retrieved successfully',
            data: UserResource::collection($users)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => bcrypt($request->validated('password')),
            'role' => $request->validated('role'),
            'company_id' => $request->user()->company_id,
        ]);

        return $this->successResponse(
            message: 'User created successfully',
            data: new UserResource($user)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        return $this->successResponse(
            message: 'User retrieved successfully',
            data: new UserResource($user->load('company'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $uuid)
    {
        $request->userToBeUpdated->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
        ]);

        return $this->successResponse(
            message: 'User updated successfully',
            data: new UserResource($request->userToBeUpdated)
        );
    }
}
