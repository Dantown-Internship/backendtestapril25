<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Get the authenticated user's company information
     */
    public function current(Request $request): JsonResponse
    {
        $company = $request->user()->company;
        
        return response()->json([
            'company' => $company,
        ]);
    }
    
    /**
     * Update the authenticated user's company information
     */
    public function update(UpdateCompanyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $company = $request->user()->company;
        
        $company->update($validated);
        
        return response()->json([
            'message' => 'Company information updated successfully',
            'company' => $company,
        ]);
    }
} 