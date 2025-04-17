<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function createUser(CreateUserRequest $request)
    {
        try {
            // get authenticated user
            $user = auth('api')->user();

            // validate request
            $request->validated();

            if ($request->role === UserRoleEnum::Admin->value) {
                abort(403, "You can not add an Admin user");
            }

            // check if company is same as admins
            if ($user->company_id != $request->company_id) {
                abort(400, "User must be from the same company");
            }

            // create user
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->company_id = $request->company_id;
            $user->role = $request->role;
            $user->save();

            return response()->json([
                "status" => "success",
                "message" => "{$request->role} addedd successfully."
            ], 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getListOfUsers()
    {
        Gate::authorize('manage-users');

        // get logged in user
        $authUser = auth('api')->user();

        // cached users query
        $users = Cache::remember('users.company.' . $authUser->company_id, 3600, function () use ($authUser) {
            return User::where(function ($query) use ($authUser) {
                $query->where('company_id', $authUser->company_id)
                    ->where('role', 'Manager')
                    ->orWhere('role', 'Employee');
            })->orderBy('created_at', 'desc')->get();
        });

        return response()->json([
            "status" => "success",
            "message" => "Request successful.",
            "data" => UserResource::collection($users)
        ], 200);
    }


    public function updateUserRole(UpdateUserRequest $request, string $userId)
    {
        try {
            $request->validated();

            $authUser = auth('api')->user();

            $user = User::where(function ($query) use ($authUser, $userId) {
                $query->where('id', $userId)->where('company_id', $authUser->company_id);
            })->first();

            if (!$user) {
                abort(404, "User not found.");
            }

            $user->role = $request->role;
            $user->save();

            // update cached user info
            $cacheKey = "users.company." . $authUser->company_id;
            Cache::forget($cacheKey);

            return response()->json([
                "status" => "success",
                "message" => "User role updated successfully."
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
