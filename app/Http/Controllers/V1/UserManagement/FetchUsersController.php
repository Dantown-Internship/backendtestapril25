<?php

namespace App\Http\Controllers\V1\UserManagement;

use App\Actions\User\ListUsersAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UserManagement\FetchUsersRequest;
use App\Http\Resources\V1\UserManagement\FetchUsersResource;

class FetchUsersController extends Controller
{
    public function __construct(
        private ListUsersAction $listUsersAction
    ) {}

    public function __invoke(FetchUsersRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        ['user_payload' => $users, 'pagination_payload' => $paginationPayload] = $this->listUsersAction->execute([
            'filter_record_options_payload' => [
                'company_id' => $loggedInUser->company_id,
                'search_query' => $request->search_query,
            ],
            'pagination_payload' => [
                'page' => $request->page ?? 1,
                'limit' => $request->per_page ?? 20,
            ]
        ]);

        $mutatedUsers = FetchUsersResource::collection($users);

        $responsePayload = [
            'users' => $mutatedUsers,
            'pagination_payload' => $paginationPayload
        ];

        return generateSuccessApiMessage('The list of users was retrieved successfully', 200, $responsePayload);
    }
}
