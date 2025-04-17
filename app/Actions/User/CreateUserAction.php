<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUserAction
{
    public function __construct(
        private User $user
    )
    {}

    public function execute(array $createUserRecordOptions)
    {
        
        return $this->user->create(
            $createUserRecordOptions
        );
    }
}