<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class AdminAuthController extends Controller
{
    use AuthorizesRequests;
    //USER ADMIN REGISTER

     public function AdminRegister(Request $req) {
        
        $req->validate([
            "name"=>"Required",
            "email"=>"Required",
            "password"=>"Required",
            "role"=>"Required",
            "company_name"=>"Required",
            "company_email"=>"Required"
            ]);

        try {
                DB::beginTransaction();
        

          $company = Company::create([
            "company_name"=>$req->company_name,
            "company_email"=>$req->company_email
          ]);

          $adminUser = User::create([
            "name"=>$req->name,
            "email"=>$req->email,
            "password"=>bcrypt($req->password),
            "company_id"=>$company->id,
            "role"=>$req->role
          ]);

          DB::commit();

          return response()->json([
              "message" => "Admin and company registered successfully.",
              "data" => [
                  "user" => $adminUser,
                  "company" => $company
              ]
          ], 201);
           }   catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin registration failed: ' . $e->getMessage());
  
          return response()->json([
              "message" => "Failed to register admin.",
              "error" => $e->getMessage()
          ], 500);
      }

     }

  //USER ADMIN LOGIN
  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $token = $user->createToken('API Token')->plainTextToken;

    return response()->json(['token' => $token]);
}




}
