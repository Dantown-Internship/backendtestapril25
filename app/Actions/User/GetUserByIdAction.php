<?php

namespace App\Actions\User;

use App\Models\User;

class GetUserByIdAction
{
    public function __construct(
        private User $user
    )
    {
        
    }
    public function execute(string $userId, array $relationships = [])
    {
        return $this->user->with(
            $relationships
        )->where([
            'id' => $userId
        ])->first();
    }
}