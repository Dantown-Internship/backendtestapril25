<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // Only admins can list users
       if (!auth()->user()->isAdmin()) {
        abort(403, 'Only admins can list users.');
    }

    $users = User::where('company_id', auth()->user()->company_id)
        ->paginate(15);

    return response()->json($users);
    }

    public function updateRole(Request $request, User $user)
    {
        // Only admins can update roles
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admins can update roles.');
        }

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->update(['role' => $request->role]);

        return response()->json($user);
    }
}
