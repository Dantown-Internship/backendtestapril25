<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {       $expense = [
        [
            "title"=>"my test expense",
            "user_id"=>1,
            "company_id"=>1,
            "amount"=>300,
            "category"=>"shoes"
        ],
        [
            "title"=>"my shoe expense",
            "user_id"=>1,
            "company_id"=>1,
            "amount"=>2500,
            "category"=>"shoes"
        ],
        [
            "title"=>"my house expense",
            "user_id"=>1,
            "company_id"=>1,
            "amount"=>3000,
            "category"=>"house"
        ],
    ];

    foreach ($expense as $key => $value) {
        $already_exist = Expense::where('title', $value['title'])->first();
        if(!$already_exist){
            Expense::create([
                "title" => $value['title'],
                "user_id" =>$value['user_id'],
                "company_id"=>$value['company_id'],
                "amount"=>$value['amount'],
                "category"=>$value['category']
            ]);
        }
    }    }
}
