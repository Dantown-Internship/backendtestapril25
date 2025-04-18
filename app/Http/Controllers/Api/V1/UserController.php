<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $users = Cache::remember("users_company_{$companyId}", 60, function () use ($companyId) {
            return User::where('company_id', $companyId)->get();
        });

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $admin = Auth::user();

        $data = $request->validated();

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->company_id = $admin->company_id;
        $user->role = $data['role'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $admin = Auth::user();

        $data = $request->validated();

        $user = User::where('company_id', $admin->company_id)->find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update(['role' => $data['role']]);

        return response()->json(['message' => 'Role updated successfully.']);
    }

}
