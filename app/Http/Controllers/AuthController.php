<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\Central\User as CentralUser;
use App\Models\Tenant\User as TenantUser;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function register(Request $request)
    {
        $this->middleware('auth:sanctum')->except('login');
        $this->middleware('role:Admin')->except('login');

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            // 'password' => 'required|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $tenant = $this->tenantService->getTenantByIdOrSubdomain($request->user()->tenant_id);
        $this->tenantService->setTenantConnection($tenant);

        if (TenantUser::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already registered for this tenant'], 422);
        }

        $centralUser = CentralUser::where('email', $request->email)->first();
        if (!$centralUser) {
            $centralUser = CentralUser::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_ids' => [$tenant->id],
            ]);
        } else {
            $tenantIds = $centralUser->tenant_ids ?? [];
            if (!in_array($tenant->id, $tenantIds)) {
                $tenantIds[] = $tenant->id;
                $centralUser->update(['tenant_ids' => $tenantIds]);
            }
        }

        $tenantUser = TenantUser::create([
            'central_user_id' => $centralUser->id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return response()->json([
            'user' => $tenantUser,
            'token' => $centralUser->createToken('api', ['tenant_id' => $tenant->id])->plainTextToken,
        ], 201);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //         'tenant_id' => 'required',
    //     ]);

    //     $tenant = $this->tenantService->getTenantByIdOrSubdomain($request->tenant_id);
    //     $this->tenantService->setTenantConnection($tenant);

    //     $centralUser = CentralUser::where('email', $request->email)->first();

    //     if (!$centralUser || !Hash::check($request->password, $centralUser->password)) {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }

    //     if (!in_array($tenant->id, $centralUser->tenant_ids ?? [])) {
    //         return response()->json(['error' => 'User not associated with this tenant'], 403);
    //     }

    //     $tenantUser = TenantUser::where('central_user_id', $centralUser->id)
    //         ->where('email', $request->email)
    //         ->firstOrFail();

    //     return response()->json([
    //         'user' => $tenantUser,
    //         'token' => $centralUser->createToken('api', ['tenant_id' => $tenant->id])->plainTextToken,
    //     ]);
    // }
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'tenant_id' => 'required|exists:tenants,id',
    ]);

    // Step 1: Get the tenant
    $tenant = $this->tenantService->getTenantByIdOrSubdomain($request->tenant_id);
    if (!$tenant) {
        return response()->json(['message' => 'Invalid tenant.'], 404);
    }

    // Step 2: Set the tenant database connection
    $this->tenantService->setTenantConnection($tenant);

    // Step 3: Authenticate the tenant user from the tenant's DB
    $tenantUser = TenantUser::where('email', $request->email)->first();

    if (!$tenantUser || !Hash::check($request->password, $tenantUser->password)) {
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    // Optional: get central user if needed
    $centralUser = CentralUser::find($tenantUser->central_user_id);

    // Step 4: Create token (or session)
    $token = $tenantUser->createToken('tenant-token')->plainTextToken;

    return response()->json([
        'user' => $tenantUser,
        'role' => $tenantUser->role ?? 'user',
        'tenant' => $tenant->name,
        'central_user' => $centralUser, // optional
        'token' => $token,
    ]);
}

}