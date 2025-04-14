<?php

namespace App\Http\Controllers\Api\Auth;

use App\Libs\Actions\Users\CreateUserAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function __construct(
        protected CreateUserAction $registerAction
    ){}

    /**
     * Register New User.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        return $this->registerAction->handle($request);
    }
}
