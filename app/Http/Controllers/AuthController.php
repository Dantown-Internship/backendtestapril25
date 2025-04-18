<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ResponseTrait;

    public function register(Request $request)
    {
        $data = $request->only(['company_name', 'user_name', 'email', 'role']);
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:255|unique:companies,name',
            'user_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            // 'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:Admin,Manager,Employee',
        ], [
            'role.in' => 'The selected role is invalid. Kindly, select between Admin, Manager or Employee'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $company = Company::create([
            'name' => $data['company_name']
        ]);

        $password = \Str::ulid();
        $user = User::create([
            'company_id' => $company->id,
            'name' => $data['user_name'],
            'password' => $password,
            ...$data
        ]);

        // Welcome email with reset password link should be sent here
        return $this->successResponse('Company registered successfully.', $user->load('company'), 201);
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse('Login successfully.', [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
