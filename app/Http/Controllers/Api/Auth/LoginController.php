<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\RoleEnum;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
           return $this->error('Invalid login credentials');
        }
        if(Auth::user()->role != RoleEnum::ADMIN->value) {
            return $this->unauthorized();
        }
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $user
        ], ucwords('login successful'));
    }
}