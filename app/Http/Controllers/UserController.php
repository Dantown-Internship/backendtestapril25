<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        // ✅ Allow only Admin
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 📦 Fetch all users in same company
        $users = User::where('company_id', $user->company_id)->get();

        return response()->json($users);
    }

}

