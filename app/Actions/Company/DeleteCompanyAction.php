<?php

namespace App\Actions\Company;

use App\Models\Company;

class DeleteCompanyAction
{
    public function __construct(
        private Company $company
    )
    {
        
    }
    public function execute(string $companyId)
    {
        return $this->company->where([
            'id' => $companyId
        ])->delete();
    }
}