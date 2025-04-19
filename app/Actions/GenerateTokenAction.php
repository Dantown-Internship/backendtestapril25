<?php

namespace App\Actions;

use App\Models\User;

class GenerateTokenAction
{
    public function execute(User $user)
    {
        $user->tokens()->delete();

        $token = $user->createToken("API TOKEN")->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'token_type' => 'Bearer',
        ];
    }

}
