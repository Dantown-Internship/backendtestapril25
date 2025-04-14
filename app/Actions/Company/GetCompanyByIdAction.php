<?php

namespace App\Actions\Company;

use App\Models\Company;

class GetCompanyByIdAction
{
    public function __construct(
        private Company $company
    )
    {
        
    }
    public function execute(string $companyId, array $relationships = [])
    {
        return $this->company->with(
            $relationships
        )->where([
            'id' => $companyId
        ])->first();
    }
}