<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserService
{
     public function getAllUser(): Collection
    {
        return User::all();
    }

   
    public function getUserById($id): ?array
    {
        $data = User::find($id);
        
        return ['success' => true, 'message' => 'fetch data successfully', 'data' => $data];
    }

    public function updateUser(int $id, array $data): array
    {
        $user = $this->getUserById($id)['data'];
        
        if (!$user) {
            return ['success' => false, 'message' => 'data not found', 'data' => []];
        }

        $isUpdated = $user->update($data);
        return ['success' => $isUpdated, 'message' => 'data updated successfully', 'data' => $user->fresh()];
        
    }

    public function updatePassword(int $id, array $data): array
    {
        $user = $this->getUserById($id)['data'];
        
        if (!$user) {
            return ['success' => false, 'message' => 'data not found', 'data' => []];
        }

        $password = bcrypt($data['password']);
        $isUpdated = $user->update(['password' => $password]);
        return ['success' => $isUpdated, 'message' => 'password updated successfully', 'data' => $user->fresh()];
        
    }

   
    public function deleteUser(int $id): array
    {
        $user = $this->getUserById($id)['data'];
        
        if (!$user) {
            return ['success' => false, 'message' => 'Failed to delete user', 'data' => []];
        }

        $isUserDeleted = $user->delete();
        return ['success' => $isUserDeleted, 'message' => 'data deleted successfully', 'data' => []];
    }
}