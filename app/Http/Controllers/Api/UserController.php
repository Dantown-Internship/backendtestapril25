<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::where('company_id', auth()->user()->company_id)->paginate(10);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => ['required', Rule::in(User::ROLES)],
        ]);
    
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => auth()->user()->company_id,
            'role' => $request->role,
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        if ($user->company_id !== auth()->user()->company_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);
    
        $user->update(['role' => $request->role]);
    
        return $user;
    }
     
}
