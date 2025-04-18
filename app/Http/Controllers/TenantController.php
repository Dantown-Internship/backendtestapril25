<?php

/// app/Http/Controllers/TenantController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TenantService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant\User as TenantUser;
use App\Models\Central\User as CentralUser;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:tenants',
                'subdomain' => 'required|string|unique:tenants',
            ]);
    
            // Create tenant and initial Admin user
            $result = $this->tenantService->createTenantWithAdmin($request->all());
    
            return response()->json([
                'tenant' => $result['tenant'],
                'admin_user' => $result['tenant_user'],
                'token' => $result['token'],
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Exception $e) {
            Log::error('Error creating tenant: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'An error occurred while creating the tenant.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

}