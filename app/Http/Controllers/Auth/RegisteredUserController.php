<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Company::class . ",email"],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // DB::beginTransaction();
        try {
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role' => 'Admin',
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json(['error' => 'Registration failed.', 'errorMessage' => $e], 500);
        }

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'role' => $request->role,
        //     'password' => Hash::make($request->string('password')),
        // ]);


        // return response()->noContent();
    }
}
