<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => ['required', 'string', Rule::in(['Admin', 'Employee', 'Manager'])]
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation failed', $validator->errors(), 422);
            }


            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => auth()->user()->company_id,
                'role' => $request->role,
            ]);

            return ResponseHelper::success($user, 'User registered successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to create user', ['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $users = User::where('company_id', auth()->user()->company_id)
                ->when($request->search, function ($q) use ($request) {
                    $q->where(function ($query) use ($request) {
                        $query->where('name', 'like', "%$request->search%")
                            ->orWhere('email', 'like', "%$request->search%")
                            ->orWhere('role', 'like', "%$request->search%");
                    });
                })
                ->paginate($request->limit ?? 10);
            return ResponseHelper::success($users, 'Users fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to fetch users', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(User $user, Request $request)
    {

        try {
            $this->authorize('update', $user);

            $validator = Validator::make($request->all(), [
                'role' => ['required', 'string', Rule::in(['Admin', 'Employee', 'Manager'])]
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation failed', $validator->errors(), 422);
            }

            $user->update(["role" => $request->role]);

            return ResponseHelper::success($user, 'Users updated successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update users', ['error' => $e->getMessage()], 500);
        }
    }

}
