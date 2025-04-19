<?php

namespace App\Actions;

use App\Data\RegisterData;
use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterAction
{
    public function execute(RegisterData $registerData)
    {
        return DB::transaction(function () use ($registerData) {
            $company = Company::create([
                'name' => $registerData->company_name,
                'email' => $registerData->company_email,
            ]);

            return User::create([
                'name' => $registerData->name,
                'email' => $registerData->email,
                'password' => bcrypt($registerData->password),
                'company_id' => $company->id,
                'role' => RoleEnum::ADMIN->value,
            ])->assignRole(RoleEnum::ADMIN->value);
        });
    }
}
