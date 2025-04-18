<?php

namespace App\Services\Services;

use App\Models\User;
use App\Services\ServiceParent;

class UserService extends ServiceParent
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function getUsersByCompanyId($companyId)
    {
        return $this->model::where('company_id', $companyId)->get();
    }

    public function createUser(array $data)
    {
        return $this->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
            'role'      => $data['role'],
            'company_id'=> $data['company_id'],
        ]);
    }

    public function updateUser($id, array $data)
    {
        return $this->update($id, [
            'name'      => $data['name'] ?? $this->model::find($id)->name,
            'email'     => $data['email'] ?? $this->model::find($id)->email,
            'password'  => $data['password'] ? bcrypt($data['password']) : $this->model::find($id)->password,
            'role'      => $data['role'] ?? $this->model::find($id)->role,
        ]);
    }
}
