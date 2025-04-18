<?php

namespace App\Services\Services;

use App\Models\User;
use App\Services\ServiceParent;

class AuthService extends ServiceParent
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function authenticate($email, $password)
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }
}
