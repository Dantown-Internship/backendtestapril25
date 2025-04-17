<?php

namespace App\Actions\Company;

use App\Models\Company;

class CreateCompanyAction
{
    public function __construct(
        private Company $company
    )
    {}

    public function execute(array $createCompanyRecordOptions)
    {
        return $this->company->create(
            $createCompanyRecordOptions
        );
    }
}