<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Http\Requests\Auth\AdminRegRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(AdminRegRequest $request): JsonResponse
    {
        // Admin registration creates companies
        if ($request->role === 'Admin') {
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->email
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'company_id' => $company->id,
                'role' => $request->role
            ]);
        }else{
            return $this->respond('Only for admin registration', statusCode: Response::HTTP_UNAUTHORIZED);

        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respond(
            'Company & Admin created successfully',
            [
                'token' => $token,
                'user' => $user
            ]
        );
    }
    
    public function login(LoginRequest $request): JsonResponse
    {   
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->respond('Invalid credentials', statusCode:Response::HTTP_UNAUTHORIZED);
        }
        $user = User::where('email', $request->email)->first();
        
        $user->tokens()->delete();
        
        return $this->respond('Login successfully',[
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ]);
    }
    
    public function logout(Request $request): JsonResponse
    {
        $user->tokens()->delete();
        return $this->respond('Logged out');
    }
}
