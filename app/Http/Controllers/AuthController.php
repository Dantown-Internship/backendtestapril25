<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,email',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed',$validator->errors(), 422);
        }
    
        DB::beginTransaction();
    
        try {
            $company = Company::create([
                'id' =>  Str::uuid(),
                'name' => $request->company_name,
                'email' => $request->company_email,
            ]);

    
            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'role' => 'Admin',
            ]);
    
            DB::commit();

            return ResponseHelper::success(['user' => $user,'company' => $company,], 'Company and admin user registered successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::error('Registration failed', ['error' => $e->getMessage()], 500);
        }
    }
    
    public function login(Request $request)
    {
      try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed',$validator->errors(), 422);
        }
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return ResponseHelper::error('Invalid credentials', [], 401);
        }
    
        $user = Auth::user();
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return ResponseHelper::success(['user' => $user,'token' => $token,], 'Login successful', 200);
      } catch (\Exception $e) {
        return ResponseHelper::error('Login failed',['error' => $e->getMessage()], 500);
      }
    }
}
