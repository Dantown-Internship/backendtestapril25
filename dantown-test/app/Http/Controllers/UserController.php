<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserController;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }
}
