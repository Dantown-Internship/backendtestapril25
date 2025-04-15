<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\Response;
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Validator as ValidationValidator;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
           'company_id' => ['required', 'exists:companies,id'], 
           'name' => ['required', 'string', 'max:255'],
           'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
           'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user = User::create([
           'company_id' => $request->company_id,
           'name' => $request->name,
           'email' => $request->email,
           'password' => Hash::make($request->password),
           'role' => 'Admin'
        ]);
    
        Log::info('New Admin Registered', ['id' => $user->id]);
    
        return response()->json([
            'message' => 'Admin registered successfully',
            'user' => $user->makeHidden(['password', 'remember_token'])
        ]);
    }
}
