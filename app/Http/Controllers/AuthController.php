<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
   {
        $this->authService = $authService;
    }

    public function register(UserRequest $request)
    {
        return $this->authService->register($request);
    }

    public function login(Request $request)
    {
        return $this->authService->login($request);
   }
}
