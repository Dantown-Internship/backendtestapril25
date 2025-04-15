<?php

namespace App\Libs\Actions\Auth;

use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Resources\UserResource;

class LoginAction
{
    public function handle($request): JsonResponse
    {
        try{            
            $credentials = $request->only('email', 'password');

            if (!auth()->attempt($credentials)) {
                throw new \Exception('Invalid credentials');
            }

            return response()->json([
                'message' => 'Login successful',
                'token' => auth()->user()->createToken('auth-token')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => UserResource::make(auth()->user()),
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