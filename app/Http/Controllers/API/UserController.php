<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @group User
 * 
 * User Related Apis
 */
class UserController extends Controller
{
    private $authService;
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * List all users
     * 
     * This endpoint returns a paginated list of all users
     * 
     * @apiResourceCollection App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User paginate=10
     */
    public function index()
    {
        Gate::authorize('viewAny', Auth::user());
        return UserResource::collection(User::paginate(10));
    }



    /**
     * Create New User
     * 
     * This endpoint creates a new user
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', Auth::user());
        $user = $this->authService->createUser($request->user(), $request->validated());
        return UserResource::make($user);
    }



    /**
     * Updates Single User
     * 
     * This endpoint updates a single user
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        $user->update($request->validated());
        return UserResource::make($user);
    }
}
