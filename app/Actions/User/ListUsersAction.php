<?php

namespace App\Actions\User;

use App\Models\User;

class ListUsersAction
{
    public function __construct(
        private User $user
    ) {}

    public function execute(array $listUserRecordOptions, array $relationships = [])
    {
        $paginationPayload = $listUserRecordOptions['pagination_payload'] ?? null;
        $filterRecordOptionsPayload = $listUserRecordOptions['filter_record_options_payload'] ?? null;

        $query = $this->user->query()
            ->with($relationships)
            ->orderBy('name', 'asc');

        if (!empty($filterRecordOptionsPayload['company_id'])) {
            $query->where('company_id', $filterRecordOptionsPayload['company_id']);
        }

        if ($paginationPayload) {
            $paginatedUsers = $query->paginate(
                $paginationPayload['limit'] ?? config('businessConfig.default_page_limit'),
                ['*'],
                'page',
                $paginationPayload['page'] ?? 1
            );

            return [
                'user_payload' => $paginatedUsers->items(),
                'pagination_payload' => [
                    'meta' => generatePaginationMeta($paginatedUsers),
                    'links' => generatePaginationLinks($paginatedUsers)
                ],
            ];
        }

        $users = $query->get();

        return [
            'user_payload' => $users,
        ];
    }
}
