<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyName = readline('Enter your company name: ');
        $userName = readline('Enter your name: ');
        $email = readline('Enter your email: ');
        $password = readline('Enter your password: ');
        $passwordConfirmation = readline('Re-enter your password: ');


        $validate = Validator::make([
            'company_name' => $companyName,
            'user_name' => $userName,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ], [
            'company_name' => 'required|string|unique:companies,name',
            'user_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8'
        ]);

        if ($validate->fails()) {
            throw new \Exception($validate->errors()->first());
        }

        $company = Company::create([
            'name' => $companyName
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => $userName,
            'email' => $email,
            'password' => $password,
            'role' => 'Admin',
        ]);
    }
}
