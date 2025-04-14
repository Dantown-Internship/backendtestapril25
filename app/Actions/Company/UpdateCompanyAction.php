<?php

namespace App\Actions\Company;

use App\Models\Company;

class UpdateCompanyAction
{
    public function __construct(
        private Company $company
    )
    {
        
    }
    public function execute(array $updateCompanyRecordOptions)
    {
        $companyId = $updateCompanyRecordOptions['id'];
        $data = $updateCompanyRecordOptions['data'];

        return $this->company->where([
            'id' => $companyId
        ])->update($data);
    }
}