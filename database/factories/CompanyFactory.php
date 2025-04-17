<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'id'         => Str::uuid(),
            'name'       => $this->faker->company,
            'email'      => $this->faker->unique()->companyEmail,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
