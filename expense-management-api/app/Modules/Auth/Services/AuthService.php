<?php

namespace App\Modules\Auth\Services;

use App\Enums\Roles;
use App\Models\Company;
use App\Models\User;
use App\Modules\Auth\Dtos\RegisterDto;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\Company\Resources\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthService
{
    public function create(RegisterDto $dto)
    {
        try {
            $userData = (array) $dto;

            // create a new company
            $company = Company::create([
                'name' => $userData['company_name'],
                'email' => $userData['company_email'],
            ]);
            
            //create user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'company_id' => $company->id,
                'role' => Roles::ADMIN->value,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'message' => 'Registered successfully!',
                'data' => [
                    'user' => new UserResource($user),
                    'company' => new CompanyResource($company),
                    "token" => $token
                ],
            ];
        } catch (Throwable $th) {
            logger('Error while registering', [$th]);
            return null;
        }
    }

    public function auth(array $validatedData)
    {
        if (!Auth::attempt($validatedData)) {
            return [
                'status' => false,
                'message' => "Invalid login credentials"
            ];
        }

        $user = User::with('company')->where('email', $validatedData['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status' => true,
            'message' => 'Login successfully!',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ];
    }

}
