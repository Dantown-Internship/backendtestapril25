<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class CompanyService
{
    /**
     * Get all companies
     *
     * @return Collection
     */
    public function getAllCompanies(): Collection
    {
        return Company::all();
    }

   
    public function getCompanyById($id): ?array
    {
        $data = Company::find($id);
        
        return ['success' => true, 'message' => 'fetch data successfully', 'data' => $data];
    }

    public function getCompanyByNameAndEmail(string $companyName, string $companyEmail): ?Company
    {
        return Company::where(['email'=> $companyEmail, 'name'=> $companyName])->first();
    }

   
    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

   
    public function updateCompany(int $id, array $data): array
    {
        $company = $this->getCompanyById($id)['data'];
        
        if (!$company) {
            return ['success' => false, 'message' => 'data not found', 'data' => []];
        }

        $isUpdated = $company->update($data);
        return ['success' => $isUpdated, 'message' => 'data updated successfully', 'data' => $company->fresh()];
        
    }

   
    public function deleteCompany(int $id): array
    {
        $company = $this->getCompanyById($id)['data'];
        
        if (!$company) {
            return ['success' => false, 'message' => 'Failed to delete company', 'data' => []];
        }

        $isCompanyDeleted = $company->delete();
        return ['success' => $isCompanyDeleted, 'message' => 'data deleted successfully', 'data' => []];
    }

    public function softDeleteCompany(int $id): array
    {
        $company = $this->getCompanyById($id)['data'];

        if (!$company) {
            return ['success' => false, 'message' => 'Failed to deactivate company', 'data' => []];
        }

        $company->is_active = false;
        $company->save();

        $company->users()->update(['is_active' => false]);

        return [
            'success' => true,
            'message' => 'Company deactivated successfully',
            'data' => [],
        ];
    }

    public function activateCompany(int $id): array
    {
        $company = $this->getCompanyById($id)['data'];

        if (!$company) {
            return ['success' => false, 'message' => 'Failed to activate company', 'data' => []];
        }

        $company->is_active = true;
        $company->save();

        $company->users()->update(['is_active' => true]);

        return [
            'success' => true,
            'message' => 'Company activated successfully',
            'data' => [],
        ];
    }
}
