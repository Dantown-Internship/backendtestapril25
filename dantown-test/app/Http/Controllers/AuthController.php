<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Services\AuthService;
use App\Exceptions\CustomApiErrorResponseHandler;
use Log;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerUser(Request $request)
    {
        $authUserCompany = Auth::user()->load('company');

        if (!$authUserCompany) {
            return response()->json(['success' => false, 'message' => 'Company information not available for the logged-in user.', 'user' => [], 'token' => null], 400);
        }
    
        $fields = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "password" => "required|min:8",
            "role" => "required"
        ]);
        $responseData = $this->authService->createUser($fields, $authUserCompany);
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    public function registerAdminUser(Request $request)
    {
        $fields = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "password" => "required|min:8",
            "companyName" => "required|string|max:255",
            "companyEmail" => "required|email",
            "role" => "required"
        ]);
        $responseData = $this->authService->createAdminUser($fields);
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users",
            "password" => "required",
        ]);
        $responseData = $this->authService->loginUser($request);
        return response()->json($responseData, $responseData['success'] ? 200:401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
         return response()->json(['message' => 'Logout Successfully'],  200);
    }
}
