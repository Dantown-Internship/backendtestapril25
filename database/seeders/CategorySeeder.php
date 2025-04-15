<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $defaultCategories = [
            'Travel',
            'Meals',
            'Office Supplies',
            'Entertainment',
            'Training & Development',
            'Software Subscriptions',
            'Utilities',
            'Marketing',
            'Client Gifts',
            'Miscellaneous'
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            foreach ($defaultCategories as $category) {
                Category::create([
                    'name' => $category,
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
