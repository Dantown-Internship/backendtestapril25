<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        // dd($request);

        try {
            // validate request
            $request->validated();

            // check if user is registering as an admin or not
            if ($request->role != UserRoleEnum::Admin->value) {
                abort(400, "You are not allowed to register.");
            }

            // store admin user information
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->company_id = $request->company_id;
            $user->role = $request->role;
            $user->save();

            return response()->json([
                "status" => "success",
                "message" => "Registration successful"
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            // validate request
            $request->validated();

            // check user credentials
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                abort(400, "Invalid credentials");
            }

            // create token
            $token = $user->createToken("$user->name")->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "Login successful",
                "token" => $token
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
