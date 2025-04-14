<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Libs\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{

    public function __construct(
        protected LoginAction $loginAction
    ){}
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);


        return $this->loginAction->handle($request);
    }
}
