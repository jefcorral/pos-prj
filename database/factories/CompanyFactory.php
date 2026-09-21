<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => str()->slug($name).'-'.str()->random(4),
            'currency' => 'PHP',
            'timezone' => 'Asia/Manila',
            'is_active' => true,
        ];
    }
}
