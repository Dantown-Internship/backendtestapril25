<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\ExpenseService;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\LoginServiceInterface;

class AuthController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);
    
        $user = $this->userService->createUser([
            ...$validated,
            'company_id' => auth()->user()->company_id
        ]);
    
        return response()->json(['message' => 'User registered', 'user' => $user]);
    }

    public function login(LoginRequest $request, LoginServiceInterface $loginService)
    {
        $data = $loginService->login($request->only('email', 'password'), $request->ip());
    
        return response()->json($data);
    }

    public function logout(LoginServiceInterface $loginService)
    {
        $loginService->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
        
}

