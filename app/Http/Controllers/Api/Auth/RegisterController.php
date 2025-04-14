<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Libs\Actions\Auth\RegisterAction;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function __construct(
        protected RegisterAction $registerAction
    ){}

    /**
     * Handle the incoming request.
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
