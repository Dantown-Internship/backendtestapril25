<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Create New User Account

    public function store(Request $request){
        try {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

            return response()->json(['sucess'=> true, 'message' => 'User Account Created Successfully', 'user'=> $user], 201);
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
}


