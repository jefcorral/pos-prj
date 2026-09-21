<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(3, true),
            'sku' => str()->upper(fake()->unique()->bothify('SKU-####')),
            'barcode' => fake()->unique()->numerify('4800#########'),
            'cost_price' => fake()->randomFloat(2, 5, 100),
            'selling_price' => fake()->randomFloat(2, 100, 500),
            'low_stock_threshold' => 5,
            'track_stock' => true,
            'is_active' => true,
        ];
    }
}
