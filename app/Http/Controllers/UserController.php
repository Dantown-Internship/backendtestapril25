<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    /**
     * Display a listing of the users, cached by company_id for 1 hour.
     */
    public function index(Request $request)
    {
        if (Gate::denies('viewAny', User::class)) {
            return $this->failure('You do not have the permission to view users.', 403);
        }

        $companyId = $request->user()->company_id;

        $cacheKey = 'users.company.' . $companyId;


        $page = $request->get('page', 1);
        $cacheKey = "users.company.{$companyId}.page.{$page}";

        // One hour (60 minutes) cache
        $users = Cache::remember($cacheKey, 60 * 60, function () use ($companyId) {
            return User::where('company_id', $companyId)->paginate(24);
        });

        return $this->success(
            UserResource::collection($users)->response()->getData(true),
            'Users fetched successfully.'
        );
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
        $this->clearUserCompanyCache($user->company_id);

        return $this->success(new UserResource($user), 'User created successfully.', 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        if (Gate::denies('viewAny', User::class)) {
            return $this->failure('You do not have the permission to view user.', 403);
        }
        return $this->success(new UserResource($user), 'User fetched successfully.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (Gate::denies('update', User::class)) {
            return $this->failure('You do not have the permission to update user', 403);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', [
                User::ADMIN,
                User::MANAGER,
                User::EMPLOYEE,
            ]),
        ], [
            'role.in' => 'The selected role is invalid. Valid roles are: ' . implode(', ', [
                User::ADMIN,
                User::MANAGER,
                User::EMPLOYEE,
            ]),
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        // Clear cache to ensure fresh data next fetch
        $this->clearUserCompanyCache($user->company_id);

        return $this->success(new UserResource($user), 'User role updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (Gate::denies('delete', User::class)) {
            return $this->failure('You do not have the permission to delete user.', 403);
        }

        $user->delete();

        // Clear cache to ensure fresh data next fetch
        $this->clearUserCompanyCache($user->company_id);

        return $this->success(null, 'User deleted successfully.');
    }

    // caches function clearing
    protected function clearUserCompanyCache($companyId)
    {
        $usersCount = User::count();
        $perPage = 24;
        $maxPages = max(1, ceil($usersCount / $perPage));

        for ($page = 1; $page <= $maxPages; $page++) {
            Cache::forget("users.company.{$companyId}.page.{$page}");
        }
    }
}
