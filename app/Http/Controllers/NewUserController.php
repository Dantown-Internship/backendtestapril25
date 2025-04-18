<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewUserController extends Controller
{
    use AuthorizesRequests;

//This Route Is For Creating New Users
public function create_user(Request $req) {
    
    $req->validate([
        "name"=>"Required",
        "email"=>"Required",
        "password"=>"Required"
    ]);
    
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
        "message" => "User registered successfully.",
        "data" => [
            "user" => $userData,
        ]
    ], 201);
}


//This Route Is For Listing Users
public function view_users() {
 $this->authorize("view",User::class);
 $user = User::where("company_id",auth()->user()->company_id)->get();
 return $user;
}

public function update_user(Request $req, $id) {

$userAuth = auth()->user();
$user = User::find($id);
$this->authorize('update', $user);

$user->name = $req->name;
$user->email = $req->email;
$user->role = $req->role;
$user->company_id = $userAuth->company->id;
$user->password = $req->password;

return $user->save();
 
}

// LOGOUT Logic
public function logout(Request $req)
{
$req->user()->currentAccessToken()->delete();
return response()->json(['message' => 'Logged out']);
}

}
