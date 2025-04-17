<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                "name"=>"my company one",
                "email"=>"infp@mycompanyone.com"
            ],
            [
                "name"=>"my company two",
                "email"=>"infp@mycompanytwo.com"
            ],
            [
                "name"=>"my company three",
                "email"=>"infp@mycompanythree.com"
            ],
        ];

        foreach ($companies as $key => $value) {
            $already_exist = Company::where('email', $value['email'])->first();
            if(!$already_exist){
                $role = new Company();
                $role->name = $value['name'];
                $role->email = $value['email'];
                $role->save();
            }
        }
    }
}
