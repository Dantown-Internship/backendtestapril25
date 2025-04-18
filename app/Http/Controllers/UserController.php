<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(UserRegisterRequest $req) {
        
        $user = auth()->user(); 
        
        $this->authorize('create',User::class);

        $userData = User::create([
            "name"=>$req->name,
            "email"=>$req->email,
            "password"=>Hash::make($req->password),
            "company_id"=>$user->company->id,
            "role"=>$req->role
        ]);

        return response()->json([
            "message" => "Admin and company registered successfully.",
            "data" => [
                "user" => $userData,
                "company" => $user->company->company_name
            ]
        ], 201);
    }
}
