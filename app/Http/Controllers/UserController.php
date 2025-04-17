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

class UserController extends Controller
{
    //
    use HttpResponses;



    public function index()
    {
        $user = auth()->user();
        return $this->success($user, 'fetched successfully');
    }

    public function addUser(UserRegister $request)
    {

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_id' => $validated['company_id'],
            'password' =>  Hash::make($validated['password']),
        ]);

        switch (strtoupper($validated['role'])) {
            case 'EMPLOYEE':
                $user->assignRole(RoleEnum::EMPLOYEE);
                break;
            case 'MANAGER':
                $user->assignRole(RoleEnum::MANAGER);
                break;
            case 'ADMIN':
                $user->assignRole(RoleEnum::ADMIN);
                break;
            default:
                $user->assignRole(RoleEnum::EMPLOYEE);
                break;
        }
        return $this->success($user, 'User created successfully.', 201);
    }


    public function getUsers(string $page = '10')
    {
        $authUser = auth()->user();

        $users = User::where('id', '!=', $authUser->id)->with('company')
            ->paginate((int) $page);

        return $this->success($users, 'Fetched users successfully');
    }



    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        switch (strtoupper($request->role)) {
            case 'EMPLOYEE':
                $user->assignRole(RoleEnum::EMPLOYEE);
                break;
            case 'MANAGER':
                $user->assignRole(RoleEnum::MANAGER);
                break;
            case 'ADMIN':
                $user->assignRole(RoleEnum::ADMIN);
                break;
            default:
                $user->assignRole(RoleEnum::EMPLOYEE);
                break;
        }

        return $this->success($user->fresh(), 'User updated successfully.', 201);
    }
}
