<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the users, cached by company_id for 1 hour.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $cacheKey = 'users.company.' . $companyId;

        // One hour (60 minutes) cache
        $users = Cache::remember($cacheKey, 60 * 60, function () use ($companyId) {
            return User::where('company_id', $companyId)->get();
        });

        return $this->success($users, 'Users fetched successfully.');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (Gate::denies('create', User::class)) {
            return $this->failure('You do not have permission to create a user.', 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:' . implode(',', [
                User::ADMIN,
                User::MANAGER,
                User::EMPLOYEE,
            ]),
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'role.in' => 'The selected role is invalid. Valid roles are: ' . implode(', ', [
                User::ADMIN,
                User::MANAGER,
                User::EMPLOYEE,
            ]),
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Clear cache for this company
        Cache::forget('users.company.' . $user->company_id);

        return $this->success(new UserResource($user), 'User created successfully.', 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return $this->success(new UserResource($user), 'User fetched successfully.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        // Clear cache to ensure fresh data next fetch
        Cache::forget('users.company.' . $user->company_id);

        return $this->success($user, 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        // Clear cache to ensure fresh data next fetch
        Cache::forget('users.company.' . $user->company_id);

        return $this->success(null, 'User deleted successfully.');
    }
}
