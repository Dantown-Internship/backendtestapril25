<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Libs\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;

/**
 * @tags Auth
 */
final class LoginController extends Controller
{

    public function __construct(
        protected LoginAction $loginAction
    ){}
    
    /**
     * Login In To Company.
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
