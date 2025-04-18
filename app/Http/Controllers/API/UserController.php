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

class UserController extends Controller
{
    private $authService;
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Auth::user());
        return UserResource::collection(User::paginate(10));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', Auth::user());
        $user = $this->authService->createUser($request->user(), $request->validated());
        return UserResource::make($user);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        $user->update($request->validated());
        return UserResource::make($user);
    }
}
