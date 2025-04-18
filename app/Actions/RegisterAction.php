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
            Company::create([
                'name' => $registerData->company_name,
                'email' => $registerData->company_email,
            ]);

            User::create([
                'name' => $registerData->name,
                'email' => $registerData->email,
                'password' => bcrypt($registerData->password),
                'company_id' => $registerData->company_id,
            ])->assignRole(RoleEnum::ADMIN->value);
        });
    }
}
