<?php

namespace App\Actions\Company;

use App\Models\Company;

class ListCompaniesAction
{
    public function __construct(
        private Company $company
    ) {}

    public function execute(array $listCompaniesRecordOptions, array $relationships = [])
    {
        $paginationPayload = $listCompaniesRecordOptions['pagination_payload'] ?? null;
        
        $query = $this->company->query()
            ->with($relationships)
            ->orderBy('name', 'asc');

        if ($paginationPayload) {
            $paginatedCompanies = $query->paginate(
                $paginationPayload['limit'] ?? config('businessConfig.default_page_limit'),
                ['*'],
                'page',
                $paginationPayload['page'] ?? 1
            );

            return [
                'company_payload' => $paginatedCompanies->items(),
                'pagination_payload' => [
                    'meta' => generatePaginationMeta($paginatedCompanies),
                    'links' => generatePaginationLinks($paginatedCompanies)
                ],
            ];
        }

        $companies = $query->get();

        return [
            'company_payload' => $companies,
        ];
    }
}
