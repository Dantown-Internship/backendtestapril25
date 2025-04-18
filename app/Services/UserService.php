<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users for a company with pagination.
     *
     * @param int $companyId
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getUsers(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        
        return User::where('company_id', $companyId)
            ->orderBy('name')
            ->paginate($perPage);
    }
    
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $data['company_id'],
            'role' => $data['role'],
        ]);
    }
    
    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [];
        
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        
        if (isset($data['role'])) {
            $updateData['role'] = $data['role'];
        }
        
        $user->update($updateData);
        
        return $user->fresh();
    }
    
    /**
     * Get company admin users.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompanyAdmins(int $companyId)
    {
        return User::where('company_id', $companyId)
            ->where('role', 'Admin')
            ->get();
    }
}
