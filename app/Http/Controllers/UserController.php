<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('company');
        $this->middleware('role:Admin')->except(['index']);
    }

    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->paginate($request->per_page ?? 15);

        return response()->json($users);
    }



public function getUsers(Request $request)
{
    // Verify admin privileges
    $authUser = Auth::user();
    if (!$authUser || $authUser->role !== 'Admin') {
        return response()->json([
            'message' => 'Unauthorized. Only admins can perform this action.',
            'success' => false
        ], 403);
    }

    // Base query with only required fields
    $query = User::select(['name', 'role', 'company_id'])
               ->orderBy('name');

    // Optional company filter
    if ($request->has('company_id')) {
        $query->where('company_id', $request->company_id);
    }

    // Optional role filter
    if ($request->has('role')) {
        $query->where('role', $request->role);
    }

    // Pagination (default 20 per page)
    $users = $query->paginate($request->per_page ?? 20);

    return response()->json([
        'success' => true,
        'data' => $users,
        'message' => 'Users retrieved successfully'
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $admin = $request->user();

        // Verify admin privileges
        if (!$admin->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin privileges required'
            ], 403);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|in:Admin,Manager,Employee',
            'company_id' => 'sometimes|exists:companies,id'
        ]);

        // Prevent self-demotion
        if ($user->id === $admin->id && isset($validated['role'])) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role'
            ], 403);
        }

        try {
            $oldData = $user->toArray();
            $user->update($validated);

            // Log the update
            AuditLog::create([
                'user_id' => $admin->id,
                'company_id' => $admin->company_id,
                'action' => 'update_user',
                'model_type' => User::class,
                'model_id' => $user->id,
                'changes' => [
                    'old' => $oldData,
                    'new' => $user->fresh()->toArray()
                ],
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User update failed',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}