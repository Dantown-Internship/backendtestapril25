<?php

namespace App\Actions\Auth;

use App\Enums\Roles;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function handle($request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|max:255|unique:companies,email',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);
        // create company
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
        ]);
        // create admin user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => Roles::ADMIN,
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return [
            'token' => $token,
            'user' => $user,
            'company' => $company
        ];
    }
}
