<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    
    public function createAdminUser(array $body)
    {
        $company = $this->companyService->createCompany(['name' => $body->companyName, 'email' => $body->companyEmail]);
        
        $user = User::create([
            'name' => $body->name,
            'email' => $body->email,
            'password' => bcrypt($body->password),
            'company_id' => $company->id,
            'role' => $body->role
        ]);
        
        $token = $user->createToken('auth_token', ['server:'.$body->role])->plainTextToken;
        return ['success'=> true, 'message' => 'Company registered successfully','data'=>['company'=> $company, 'user'=> $user],'token' => $token];
        
    }

    public function createUser(array $body)
    {
        // TODO: company details can be gotten from login user token
        $company = $this->companyService->getCompanyByNameAndEmail($body->companyName, $body->companyEmail);
        if($company){
            $user = User::create([
                'name' => $body->name,
                'email' => $body->email,
                'password' => bcrypt($body->password),
                'company_id' => $company->id,
                'role' => $body->role
            ]);
            $token = $user->createToken('auth_token', ['server:'.$body->role])->plainTextToken;
            return ['success'=> true, 'message' => 'User registered successfully','user'=> $user,'token' => $token];
        }
       return ['success'=> false, 'message' => 'company not found','user'=> [],'token' => []];
    }

    public function loginUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $abilities = [];
            
            if ($user->role === 'SuperAdmin') {
                $abilities = ['server:superadmin'];
            } elseif ($user->role === 'Admin') {
                $abilities = ['server:admin'];
            }  elseif ($user->role === 'Manager') {
                $abilities = ['server:manager'];
            }
            else {
                $abilities = ['server:employee'];
            }
            $token = $user->createToken('auth_token', $abilities)->plainTextToken;
            return ['success'=> true, 'message' => 'User login successfully','user'=> $user,'token' => $token];
        }

        return ['success'=> false, 'message' => 'Invalid login credential','user'=> [],'token' => []];
    }
}
