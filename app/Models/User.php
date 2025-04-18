<?php

// app/Http/Controllers/TenantController.php
namespace App\Http\Controllers;

use App\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:SuperAdmin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:tenants',
        ]);

        $tenant = $this->tenantService->createTenant($request->all());

        return response()->json($tenant, 201);
    }
}