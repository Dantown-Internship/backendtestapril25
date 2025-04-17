<?php

namespace App\Contracts;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserInterface
{


    public function users(): LengthAwarePaginator;

    public function user(string $userId): ?User;

    public function update(string $id, array $data): User;

    public function delete(string $userId): bool;

    public function create(array $data, string $roleName): User;

}
