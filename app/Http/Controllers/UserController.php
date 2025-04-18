<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $AdminUser = Auth::user();

        $users = User::where("company_id", $AdminUser->company_id)->with('expenses',)->paginate(10);

        return response()->json([
            'message' => 'Successfully',
            'data' => $users,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $adminUser = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string']
        ]);

        try {
            $user = User::create([
                'company_id' => $adminUser->company_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role' => $request->role,
            ]);

            return response()->json([
                'message' => 'User added successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating expense',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $adminUser = Auth::user();

        $request->validate([
            'role' => ['required', 'string']
        ]);

        try {
            $user = User::where('id', $id)
                ->where('company_id', $adminUser->company_id)
                ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No user found',
                ], 404);
            }

            $user->update([
                'role' => $request->role,
            ]);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $adminUser = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string']
        ]);

        try {

            $user = User::where('id', $id)
                ->where('company_id', $adminUser->company_id)
                ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
