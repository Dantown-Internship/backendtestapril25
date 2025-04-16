<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{

    public $userService;
    public $authService;


    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function show()
    {
        return $this->userService->users();
    }

    public function store(RegisterRequest $request)
    {
        return $this->userService->store($request->validated());
    }

    public function update(Request $request, $id)
    {

        return $this->userService->update($request, $id);
    }
}
