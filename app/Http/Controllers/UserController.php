<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Models\User;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Controller
 *
 * Handles user-related operations including:
 * - Listing users within a company
 * - Updating user roles
 * - Managing user passwords
 *
 * All operations are scoped to the authenticated user's company.
 * Most operations require Admin privileges.
 */
class UserController extends Controller
{
    use JsonResponseTrait;

    /**
     * Display a paginated list of users.
     *
     * @param UserIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;
        $companyId = Auth::user()->company_id;
        $cacheKey = "users:company:{$companyId}:page:{$page}:per_page:{$perPage}";

        if (isset($validated['search'])) {
            $cacheKey .= ":search:{$validated['search']}";
        }
        if (isset($validated['role'])) {
            $cacheKey .= ":role:{$validated['role']}";
        }

        $users = Cache::remember($cacheKey, 3600, function () use ($validated, $companyId, $page, $perPage) {
            $query = User::where('company_id', $companyId);

            if (isset($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if (isset($validated['role'])) {
                $query->where('role', $validated['role']);
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        });

        return $this->successResponse($users, 'Users retrieved successfully');
    }

    /**
     * Store a newly created user.
     *
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::create([
            'company_id' => Auth::user()->company_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Clear all user-related caches for this company
        $companyId = Auth::user()->company_id;
        Cache::forget("users:company:{$companyId}:*");

        return $this->successResponse($user, 'User created successfully', 201);
    }

    /**
     * Update the specified user.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        if ($user->company_id !== Auth::user()->company_id) {
            return $this->forbiddenResponse('You do not have permission to update this user');
        }

        $validated = $request->validated();
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);

        // Clear all user-related caches for this company
        $companyId = Auth::user()->company_id;
        Cache::forget("users:company:{$companyId}:*");

        return $this->successResponse($user, 'User updated successfully');
    }

    /**
     * Update the authenticated user's password.
     *
     * @param PasswordUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(PasswordUpdateRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            if (!Hash::check($data['current_password'], $user->password)) {
                return $this->errorResponse('Current password is incorrect');
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            return $this->successMessage('Password updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update password: ' . $e->getMessage());
        }
    }

    /**
     * Clear the cached users for a company.
     *
     * @param int $companyId
     * @return void
     */
    public function clearCompanyUsersCache(int $companyId)
    {
        Cache::tags(['users'])->flush();
    }
}
