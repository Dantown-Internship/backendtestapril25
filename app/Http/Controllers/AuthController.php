<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(),$rules,[
            'email.required' => 'The email is required',
            'email.email' => 'The email is invalid',
            'password.required' => 'The password is required'
        ]);

        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        $user = User::with(['company'])->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return (new UserResource($user))->additional([
            'token' => $token,
        ]);
    }


    public function register(Request $request) {

        $rules = [
            'firstname'=> 'required|string|max:255',
            'lastname'=> 'required|string|max:255',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            'role'=> 'required|in:Admin,Manager,Employee',
        ];

        $messages = [
            'firstname.required'=> 'First name is required.',
            'firstname.string'=> 'First name must be a string.',
            'firstname.max'=> 'First name must not be more than 255 characters.',

            'lastname.required'=> 'Last name is required.',
            'lastname.string'=> 'Last name must be a string.',
            'lastname.max'=> 'Last name must not be more than 255 characters.',

            'email.required'=> 'Email address is required.',
            'email.email'=> 'Please enter a valid email address.',
            'email.unique'=> 'This email address is already in use.',

            'password.required'=> 'Password is required.',
            'password.min'=> 'Password must be at least 6 characters long.',

            'role.required'=> 'User role is required.',
            'role.in'=> 'Role must be either Admin, Manager or Employee.',
        ];

        // Validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Handle failed validation
        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        $user = User::create([
            'firstname'       => $request->firstname,
            'lastname'       => $request->lastname,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $request->company_id,
            'role'       => $request->role,
        ]);

        return new UserResource($user);
    }
}
