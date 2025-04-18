<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyAccountRequest;
use App\Http\Requests\RegisterAccountRequest;
use App\Http\Requests\UserAccountRequest;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Create User Account

    public function register(UserAccountRequest $request){
        DB::beginTransaction();
        try {
        //Register company Account
        $company = new Company();
        $company->company_name = $request->company_name;
        $company->company_email = $request->company_email;
        $company->save();

        //create Admin User
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = $company->id;
        $user->password = Hash::make($request->password);
        $user->role = User::$Admin;
        $user->save();

        DB::commit();
        Auth::login($user);

        $tokenResult = $user->createToken('token_name');
        $token = $tokenResult->plainTextToken;
        $expiration = Carbon::now()->addHour(8);
        $tokenResult->accessToken->expires_at = $expiration;
        $tokenResult->accessToken->save();

            return response()->json(['sucess'=> true, 'message' => 'Company Account Created', 'company'=> $company,'user'=> $user, 'token' => $token,], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
        
    }

    public function login(Request $request){
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $validate['email'];
        $password = $validate['password'];

        $user = User::where('email', $email)->first();

        if(!$user){
            return response()->json(['sucess' => false, 'message' => 'Email Address Not Found']);
        }

        if(!Hash::check($password, $user->password)){
            return response()->json(['sucess' => false, 'message' => 'InCorrect Password.']);
        }

        $tokenResult = $user->createToken('token_name');
        $token = $tokenResult->plainTextToken;
        $expiration = Carbon::now()->addHour(8);
        $tokenResult->accessToken->expires_at = $expiration;
        $tokenResult->accessToken->save();

        return response()->json(['sucess'=> true, 'message' => 'Account Login Successful', 'token' => $token,], 200);
    }


    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $response = response()->json([
            "success" => true,
            "message" => "Logout Successfully"
        ]);
        return $response;
    }
}


