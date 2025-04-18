<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::orderBy('created_at', 'ASC')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:Admin,Manager,Employee',
            'company_id' => 'required|exists:companies,id',
        ]);

        $data['password'] = bcrypt($data['password']);

        try {
            $user = DB::transaction(function () use ($data) {
                return User::create($data);
            });

            return response()->json($user, 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $data = $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->update($data);
        return $user;
    }
}