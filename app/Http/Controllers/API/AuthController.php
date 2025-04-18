<?php

namespace App\Http\Controllers\API;

use App\Constants\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ResponseTrait;

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (! Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return $this->badRequestResponse('Invalid credentials. Please register or try again.');
            }

            $user = Auth::user();

            $token =  $user->createToken($user->email)->plainTextToken;
            $token = $this->cleanToken($token);

            return $this->successResponse('Login successful', [
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'login unsuccessful');
        }
    }

    public function register(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $request->company_id,
                'role' => UserRole::ADMIN,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            $token = $user->createToken('API TOKEN')->plainTextToken;
            $token = $this->cleanToken($token);

            return $this->successResponse('registration succesful', [
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'registration unsuccessful');
        }
    }

    private function cleanToken($token)
    {
        return (explode('|', $token))[1];
    }
}
