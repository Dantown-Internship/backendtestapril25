<?php

namespace App\Actions\User;

use App\Models\User;

class GetUserByEmailAction
{
    public function __construct(
        private User $user
    )
    {
        
    }
    public function execute(string $email, array $relationships = [])
    {
        return $this->user->with(
            $relationships
        )->where([
            'email' => $email
        ])->first();
    }
}