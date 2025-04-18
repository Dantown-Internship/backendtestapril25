<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Role;
use App\Helpers\CacheKey;
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
        $role = Role::tryFrom($request->validated('role'));
        $users = User::when(
            ! blank($search),
            fn (Builder $builder) => $builder->where('name', 'LIKE', "%$search%"))
            ->when(! blank($role), fn (Builder $builder) => $builder->where('role', $role))
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
        $role = Role::from($request->validated('role'));
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => bcrypt($request->validated('password')),
            'role' => $role,
            'company_id' => $request->user()->company_id,
        ]);

        if ($role === Role::Admin) {
            cache()->forget(CacheKey::companyAdmins($user->company_id));
        }

        return $this->successResponse(
            message: 'User created successfully',
            data: new UserResource($user),
            statusCode: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->successResponse(
            message: 'User retrieved successfully',
            data: new UserResource($user->load('company'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $role = Role::from($request->validated('role'));
        $previousRole = $user->role;
        $roleChanged = $previousRole !== $role;
        $user->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $role,
        ]);
        if ($roleChanged && ($role === Role::Admin || $previousRole === Role::Admin)) {
            cache()->forget(CacheKey::companyAdmins($user->company_id));
        }

        return $this->successResponse(
            message: 'User updated successfully',
            data: new UserResource($user)
        );
    }
}
