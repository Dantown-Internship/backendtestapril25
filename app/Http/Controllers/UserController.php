<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Register a new Admin and Company
     * Only Admins can register
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,email',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return $this->respones($this->formatError($validator), null, 500);
        }
        try {
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
            ]);

            $user = $company->users()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'Admin',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->respones(
                'Company and Admin registered successfully.',
                [
                    'user' => $user,
                    'token' => $token
                ],
                201
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while creating user", null, 500);
        }
    }

    /**
     * User Login method
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        try {
            // get user based on the email provided
            $user = User::where('email', $request->email)->first();
            // Check if $user exists (not null) and the hashed provided password matches the one in the $user object. 
            // That is, if user is null or password not checked the throw Invalid username or password. 
            // I throwed 'Invalid username or password.' because I do not want to user to know that there email is correct and only the password is not correct.
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->respones("Invalid username or password.", null, 500);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->respones(
                'Loggin succeed.',
                [
                    'user' => $user,
                    'token' => $token
                ],
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while loging in user", null, 500);
        }
    }

    /**
     * List all users (Admin only)
     */
    public function index(Request $request)
    {
        try {
            // Cache the result for 60 seconds
            $users = Cache::remember('users_list', 60, function () {
                return User::with('company')->where("company_id", companyID())->get();
            });

            return $this->respones('Users retrieved successfully.', ['users' => $users], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while loading user", null, 500);
        }
    }
    /**
     * Create a new user (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        if ($validator->fails()) {
            return $this->respones($this->formatError($validator), null, 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
                'company_id' => companyID(),
            ]);

            Cache::forget('users_list');

            return $this->respones('User created successfully.', ['user' => $user], 201);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones('An error occurred while creating the user.', null, 500);
        }
    }
    /**
     * Update user (Admin only)
     */
    public function update(Request $request, Int $userID)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $userID,
            'password' => 'nullable|string|confirmed|min:6',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        if ($validator->fails()) {
            return $this->respones($this->formatError($validator), null, 400);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }
            $user = User::where("id", $userID)->firstOrFail();
            $user->update($data);

            Cache::forget('users_list');

            return $this->respones('User updated successfully.', ['user' => $user], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones('An error occurred while updating the user.', null, 500);
        }
    }
}