<?php

namespace App\Http\Controllers;


use App\Enums\RoleEnum;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegister;
use App\Models\Company;
use App\Models\User;
use App\Traits\HttpResponses;
use Faker\Guesser\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    use HttpResponses;

    public function register(AdminRegisterRequest $request)
    {

        $validated = $request->validated();
        $company = Company::create([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
        ]);

        $user = $company->users()->create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' =>  Hash::make($validated['password']),
        ]);
        $user->assignRole(RoleEnum::ADMIN);
        return $this->success([$user, $company], 'User created successfully.', 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->with('company')->first();

            if (!$user) {
                return $this->badRequest($user, "invalid user");
            }

            if (!Hash::check($validated['password'], $user->password)) {
                return $this->badRequest("invalid password");
            }

            $tokenName = 'auth-token-' . $user->id;
            $token = $user->createToken($tokenName)->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token,
                'message' => "login successfully",
            ], 201);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }


    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success([],'Successfully logged out');
    }
}
