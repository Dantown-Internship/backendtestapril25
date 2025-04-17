<?php

namespace App\Actions\User;

use App\Models\User;

class UpdateUserAction
{
    public function __construct(
        private User $user
    )
    {
        
    }
    public function execute(array $updateUserRecordOptions)
    {
        $userId = $updateUserRecordOptions['id'];
        $data = $updateUserRecordOptions['data'];

        return $this->user->where([
            'id' => $userId
        ])->update($data);
    }
}