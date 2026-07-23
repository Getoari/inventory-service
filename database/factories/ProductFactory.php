<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'category' => fake()->randomElement([
                'Electronics',
                'Office',
                'Furniture',
                'Accessories',
                'Networking',
            ]),
            'price' => fake()->randomFloat(2, 5, 1000),
            'stock_quantity' => fake()->numberBetween(0, 100),
        ];
    }
}