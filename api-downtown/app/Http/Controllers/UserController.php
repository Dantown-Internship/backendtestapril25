<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    

    public function listUsers(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse('You must be logged in to access this resource.', 401);
            }

            $cacheKey = 'users_' . $user->company_id . '_' . md5($request->fullUrl());

            $users = Cache::remember($cacheKey, 600, function () use ($user) {
                return User::where('company_id', $user->company_id)
                    ->with('company')
                    ->paginate(10);
            });

            return $this->successResponse('Users retrieved successfully', $users);
        } catch (\Exception $e) {
            Log::error('Error in listUsers', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => optional(Auth::user())->id,
            ]);

            return $this->errorResponse('An error occurred while retrieving users.', 500);
        }
    }


    public function storeUsersData(Request $request)
    {
        try {
            Log::info('Starting user creation process.', ['requested_by' => Auth::id()]);
    
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:Admin,Manager,Employee',
            ]);
    
            Log::info('Validation passed.', ['input' => $request->only(['name', 'email', 'role'])]);
    
            $user = Auth::user();
    
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $user->company_id,
                'role' => $request->role,
            ]);
    
            Log::info('User created successfully.', ['new_user_id' => $newUser->id]);
    
            return $this->successResponse('User created successfully', $newUser, 201);
    
        } catch (ValidationException $e) {
            Log::warning('Validation failed during user creation.', ['errors' => $e->errors()]);
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
    
        } catch (\Exception $e) {
            Log::error('Exception during user creation.', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse('Failed to create user', 500);
        }
    }



   public function updateRole(Request $request, $id)
{
    try {
        Log::info('Starting role update process.', [
            'requested_by' => Auth::id(),
            'target_user_id' => $id,
            'input' => $request->only('role')
        ]);

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        Log::info('Validation passed for role update.', ['role' => $request->role]);

        $user = Auth::user();
        $targetUser = User::where('company_id', $user->company_id)->findOrFail($id);

        $targetUser->update(['role' => $request->role]);

        Log::info('User role updated successfully.', [
            'updated_user_id' => $targetUser->id,
            'new_role' => $targetUser->role
        ]);

        return $this->successResponse('User role updated successfully', $targetUser);

    } catch (ValidationException $e) {
        Log::warning('Validation failed during role update.', ['errors' => $e->errors()]);
        return $this->errorResponse($e->getMessage(), 422, $e->errors());

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('User not found for role update.', ['user_id' => $id]);
        return $this->errorResponse('User not found', 404);

    } catch (\Exception $e) {
        Log::error('Exception during role update.', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return $this->errorResponse('Failed to update user role', 500);
    }
}

    protected function successResponse(string $message, $data = [], int $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse(string $message, int $status, array $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}