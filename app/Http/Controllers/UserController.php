<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;

use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Interfaces\UserServiceInterface;


class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}
    
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)->get();
        return UserResource::collection($users);
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($request->validated(), $user);
    
        return response()->json(['message' => 'User updated']);
    }

}
