<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\RegisterUser;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register Admin
     */
    public function register(Request $request)
    {
        $action = new RegisterUser();
        $data = $action->handle($request);
        // Successful Registration
        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => $data,
        ]);
    }

    /**
     * Account Login
     */
}
