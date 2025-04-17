<?php

namespace App\Services;

use App\Models\User;

use Hash;
use Illuminate\Http\Request;
use Log;

class AuthService
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    
    public function createAdminUser(array $body)
    {
        $company = $this->companyService->createCompany(['name' => $body['companyName'], 'email' => $body['companyEmail']]);
        $user = User::create([
            'name' => $body['name'],
            'email' => $body['email'],
            'password' => bcrypt($body['password']),
            'company_id' => $company->id,
            'role' => $body['role']
        ]);
        
        $token = $user->createToken('auth_token', ['server:'.$body['role']])->plainTextToken;
        return ['success'=> true, 'message' => 'Company registered successfully','data'=>['company'=> $company, 'user'=> $user],'token' => $token];
        
    }

    public function createUser(array $body, $authUserCompany)
    {

        $user = User::create([
            'name' => $body['name'],
            'email' => $body['email'],
            'password' => bcrypt($body['password']),
            'company_id' => $authUserCompany->id,
            'role' => $body['role'],
        ]);

        $token = $user->createToken('auth_token', ['server:' . $body['role']])->plainTextToken;

        return [
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user,
            'token' => $token,
        ];
    }

    public function loginUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['success' => false, 'message' => 'Invalid login credentials', 'user' => [], 'token' => null];
        }

        $company = $user->company;
        $abilities = match ($user->role) {
            'SuperAdmin' => ['server:superadmin'],
            'Admin' => ['server:admin'],
            'Manager' => ['server:manager'],
            default => ['server:employee'],
        };

        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return [
            'success' => true,
            'message' => 'User login successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'companyName' => $company->name,
                'companyEmail' => $company->email,
            ],
            'token' => $token,
        ];
    }
}
