<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 1, 200);

        return [
            'category_id' => null,
            'name' => ucwords(fake()->unique()->words(2, true)),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'description' => fake()->sentence(),
            'price' => round($cost * 1.4, 2),
            'cost' => $cost,
            'stock_quantity' => fake()->numberBetween(0, 100),
            'status' => Product::STATUS_ACTIVE,
        ];
    }
}
