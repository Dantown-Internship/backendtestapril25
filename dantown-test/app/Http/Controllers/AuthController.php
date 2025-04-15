<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        // user belong to a company
        // Registration logic (Admin only)

        $body = $request->all();

        $validator = Validator::make($body, [
            "username" => "required|string",
            "password" => "required|string",
        ], [
            'username.required' => 'Kindly provide your email address or hmo id.',
            'password.required' => 'Kindly provide your password.',
        ]);

        if ($validator->fails()) {
            throw new CustomApiErrorResponseHandler($validator->errors()->first());
        }

        $isMobile = $request->header('App-Name') !== null ? true : false;
        $data = $this->accountsRepository->accountLogin($body, $isMobile);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json(['token' => $user->createToken('SaaSApp')->plainTextToken]);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        // Registration logic (Admin only)
    }
}
