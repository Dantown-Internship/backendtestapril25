<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            return new JsonResponse(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $users = User::where('company_id', $user->company_id)
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->paginate($request->per_page ?? 15);

        return new JsonResponse(
            [
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ],
            Response::HTTP_OK
        );
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            return new JsonResponse(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users'),
            ],
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'manager', 'employee'])],
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'company_id' => $user->company_id,
        ]);

        // Assign Spatie role based on the user's role
        match ($validated['role']) {
            'admin' => $newUser->assignRole('admin'),
            'manager' => $newUser->assignRole('manager'),
            'employee' => $newUser->assignRole('employee'),
        };
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $adminUser = auth()->user();

        if (!$adminUser->isAdmin()) {
            return new JsonResponse(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_FORBIDDEN
            );
        }


        if ($adminUser->id === $user->id) {
            return new JsonResponse(
                [
                    'message' => 'You cannot update your own account',
                ],
                Response::HTTP_FORBIDDEN
            );
        }
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['sometimes', Rule::in(['admin', 'manager', 'employee'])],
        ]);

        $user->update($request->only('name', 'email', 'role'));

        if (isset($validated['role']) && $user->role !== $validated['role']) {
            // Remove the old role
            $user->syncRoles([]);

            // Assign the new role
            match ($validated['role']) {
                'admin' => $user->assignRole('admin'),
                'manager' => $user->assignRole('manager'),
                'employee' => $user->assignRole('employee'),
            };
        }

        return new JsonResponse(
            [
                'message' => 'User updated successfully',
                'data' => $user,
            ],
            Response::HTTP_OK
        );
    }
}
