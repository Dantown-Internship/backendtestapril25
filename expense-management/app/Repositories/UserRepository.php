<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\User\CreateRequest;

class UserRepository
{
    public function get(int $companyId, int $perPage = 10)
    {
        $cacheKey = "company_users_{$companyId}";
        
        return Cache::remember($cacheKey, now()->addHour(), function() use ($companyId, $perPage) {
            $query = User::where('company_id', $companyId)
                        ->select('id', 'name', 'email', 'role', 'company_id')
                        ->with(['company:id,name']);
            
            
            return $query->paginate($perPage);
        });
    }

    public function create(CreateRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => auth()->user()->company_id,
            'role' => $request->role
        ];
        $user = User::create($data);
        $this->clearCompanyCache($data['company_id']);
        return $user;
    }

    public function update(User $user, $data)
    {
        $user->update($data);
        $this->clearCompanyCache($user->company_id);
        return $user;
    }

    public function clearCompanyCache(int $companyId)
    {
        Cache::forget("company_users_{$companyId}");
    }
}