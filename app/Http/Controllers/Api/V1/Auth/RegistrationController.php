<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DataTransferObjects\TokenDto;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function __invoke(RegistrationRequest $request)
    {
        try {
            $user = DB::transaction(function () use ($request) {
                $company = Company::create([
                    'name' => $request->validated('company_name'),
                    'email' => $request->validated('company_email'),
                ]);
                $user = $company->users()->create([
                    'name' => $request->validated('name'),
                    'email' => $request->validated('email'),
                    'password' => bcrypt($request->validated('password')),
                    'role' => Role::Admin,
                ]);

                return $user;
            });
        } catch (\Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());

            return $this->errorResponse(message: 'Registration failed', statusCode: 500);
        }

        return $this->successResponse(
            message: 'Registration successful',
            data: [
                'user' => new UserResource($user->load('company')),
                'token' => (new TokenDto($user->createToken('auth_token')->plainTextToken))->toArray(),
            ],
            statusCode: 201
        );
    }
}
