<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccountRequest;
use App\Models\User;
use App\utility\Util;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function registerUser(UserAccountRequest $request){
        try {
            $user = Util::Auth();
            //Admin Create A New Company User
            $addUser = User::createRecord($request, $user->company_id);
            return response()->json(['sucess' => true, 'message' => 'New User '. $addUser->role. 'Added Successfully', 'addUser' => $addUser]);

        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }


    public function listUser(){
        try {
            
            $user = Util::Auth();
            //Admin get list of  all Company Users
            $userList = User::getUserList($user);
            
            return response()->json(['sucess' => true, 'message' => 'Fetch Users List Successfully', 'userList' => $userList]);

        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }

    public function updateUser(Request $request, $user){
        try {
            
            $this->authorize('update', $user);
            //Admin Update Company Users Role
            $updateDetails = User::find($user);
            $updateDetails->role = $request->role ?? $updateDetails->role;
            $updateDetails->update();
            
            
            return response()->json(['sucess' => true, 'message' => 'User Role Update Successfully', 'updateDetails' => $updateDetails]);

        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }


}
