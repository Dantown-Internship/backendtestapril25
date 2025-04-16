<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

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

    /**
     * Get company by ID
     *
     * @param int $id
     * @return Company|null
     */
    public function getCompanyById(int $id): ?Company
    {
        return Company::find($id);
    }

    /**
     * Create a new company
     *
     * @param array $data
     * @return Company
     */
    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Update an existing company
     *
     * @param int $id
     * @param array $data
     * @return Company|null
     */
    public function updateCompany(int $id, array $data): ?Company
    {
        $company = $this->getCompanyById($id);
        
        if (!$company) {
            return null;
        }

        $company->update($data);
        return $company->fresh();
    }

    /**
     * Delete a company
     *
     * @param int $id
     * @return bool
     */
    public function deleteCompany(int $id): bool
    {
        $company = $this->getCompanyById($id);
        
        if (!$company) {
            return false;
        }

        try {
            return $company->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete company: ' . $e->getMessage());
            return false;
        }
    }
}
