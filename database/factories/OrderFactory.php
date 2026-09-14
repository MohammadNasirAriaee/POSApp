<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 5, 500);
        $tax = round($subtotal * 0.1, 2);

        return [
            'customer_id' => null,
            'employee_id' => null,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'total' => $subtotal + $tax,
            'payment_method' => fake()->randomElement(['cash', 'card', 'bank_transfer']),
            'status' => Order::STATUS_COMPLETED,
        ];
    }
}
