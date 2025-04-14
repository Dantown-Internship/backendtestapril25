<?php

namespace App\Libs\Actions\Auth;

use App\Models\User;

class LoginAction
{
    public function handle($request)
    {
        try{
            $credentials = $request->only('email', 'password');

            if (!auth()->attempt($credentials)) {
                throw new \Exception('Invalid credentials');
            }

            return response()->json([
                'message' => 'Login successful',
                'token' => auth()->user()->createToken()->plainTextToken,
                'token_type' => 'Bearer',
                'user' => auth()->user(),
                'success' => true
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 401);

        }
          
    }
}