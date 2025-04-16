<?php

namespace App\Services;

class AuthService
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    
    public function createUser($body)
    {
        // first fetch company from company table
        // if company not exist, create it
        // user belong to a company
        // Registration logic (Company Admin only)
        // it is only company Admin that can create user on the company
        // before user can be created
        // admin user need to login
        //company Id is needed
        // admin user details is needed

        $company = Company::create($request->only('name', 'email'));
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => $company->id,
            'role' => 'Admin'
        ]);
        return response()->json(['token' => $user->createToken('auth_token')->plainTextToken]);
    }

    public function loginUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return ['success'=> true, 'message' => 'User login successfylly','user'=> $user,'token' => $user->createToken($user->name)->plainTextToken];
        }
        return ['success'=> false, 'message' => 'Invalid login credential','user'=> [],'token' => []];
    }
}
