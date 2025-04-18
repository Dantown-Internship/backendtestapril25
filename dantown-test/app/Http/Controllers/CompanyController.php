<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;

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
        
        return response()->json(['success' => true, 'message' => 'fetch data successfully', 'data' => $companies]);
    }

    public function viewCompany(int $id): JsonResponse
    {
        $authUser = Auth::user()->load('company');

        $company = $this->companyService->getCompanyById($authUser->company_id);
       
       return response()->json($company, 200);
    }

    /**
     * Update the specified company.
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateCompany(Request $request, int $id): JsonResponse
    {
        $updateRequest = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email",
        ]);
        $responseData = $this->companyService->updateCompany($id, $updateRequest);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    /**
     * Remove the specified company.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCompany(int $id): JsonResponse
    {
        $responseData = $this->companyService->deleteCompany($id);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    public function deactivateCompany(int $id): JsonResponse
    {
        $responseData = $this->companyService->softDeleteCompany($id);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }

    public function activateCompany(int $id): JsonResponse
    {
        $responseData = $this->companyService->activateCompany($id);
        
        return response()->json($responseData, $responseData['success'] ? 200:400);
    }
}
