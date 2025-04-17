<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //list all users
    public function index()
    {
        $user = auth()->user();

        //handled by middleware but redudancy
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        //scoped to users company id
        $users = User::where('company_id', $user->company_id)->get();
    
        return response()->json($users);
    }

    //add users
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'company_id' => 'required|exists:companies,id',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'role' => $request->role,
        ]);

        return response()->json(['user' => $user], 201);
    }

    //update users
    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])],
        ]);

        $user = User::find($id);
       
        $user = User::find($id);

        //scoped to only users company
        if (!$user || $user->company_id !== $admin->company_id) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->role = $request->role;
        $user->save();

        return response()->json(['user' => $user]);
    }

}
