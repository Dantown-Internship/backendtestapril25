<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)->get();
        return response()->json($users);
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found or unauthorized'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found or unauthorized'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:Admin,Manager,Employee',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'User not found or unauthorized'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
