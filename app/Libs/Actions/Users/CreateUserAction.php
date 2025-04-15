<?php

namespace App\Libs\Actions\Users;

use App\Http\Resources\ExpenseResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CreateUserAction
{
    public function handle($request): ExpenseResource|JsonResource
    {
        DB::beginTransaction();
        try{

            $data = $request->only('name', 'email', 'password');

            $data['password'] = bcrypt($data['password']);
            $data['company_id'] =  $request->currentCompany->id;

            dd($data);

            $user = User::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Registration successful',
                'data' => $user,
                'success' => true
            ], 201);

        }catch(\Exception $e){
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed',
                'success' => false
            ], 500);
        }
    }
}