<?php

namespace App\Libs\Actions\Expenses;

class GetUsersAction
{
    public function handle($request)
    {
        $user = $request->user();
        $company = $request->currentCompany;

        $users = $company->users()->paginate();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
            'success' => true
        ], 200);
    }
}