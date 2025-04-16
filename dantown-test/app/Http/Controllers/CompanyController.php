<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(): JsonResponse
    {
        $companies = $this->companyService->getAllCompanies();
        
        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }

    public function registerCompany(StoreCompanyRequest $request): JsonResponse
    {
        $company = $this->companyService->createCompany($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
            'data' => $company
        ], Response::HTTP_CREATED);
    }

    public function viewCompany(int $id): JsonResponse
    {
        $company = $this->companyService->getCompanyById($id);
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'Company not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $company
        ]);
    }

    /**
     * Update the specified company.
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateCompany(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        $company = $this->companyService->updateCompany($id, $request->validated());
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'Company not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Company updated successfully',
            'data' => $company
        ]);
    }

    /**
     * Remove the specified company.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCompany(int $id): JsonResponse
    {
        $deleted = $this->companyService->deleteCompany($id);
        
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Company not found or could not be deleted'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Company deleted successfully'
        ]);
    }
}
