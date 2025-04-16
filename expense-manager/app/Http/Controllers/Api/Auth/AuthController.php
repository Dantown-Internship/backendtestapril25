<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\RegisterUser;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register Admin
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $action = new RegisterUser;
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|max:255|unique:companies,email',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        $data = $action->handle($request);

        // Successful Registration
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => $data,
        ],201);
    }

    /**
     * Account Login
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $action = new LoginUser;
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        $data = $action->handle($request);

        // Successful Login
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => $data,
        ]);
    }

    /**
     * User Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logout Successful');
    }
}
