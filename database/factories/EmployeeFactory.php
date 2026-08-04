<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = Employee::POSITIONS;

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'position' => fake()->randomElement($positions),
            'salary' => fake()->randomFloat(2, 2200, 6500),
            'hire_date' => fake()->dateTimeBetween('-4 years', 'now'),
            'status' => fake()->randomElement([
                Employee::STATUS_ACTIVE,
                Employee::STATUS_ACTIVE,
                Employee::STATUS_ACTIVE,
                Employee::STATUS_ON_LEAVE,
                Employee::STATUS_INACTIVE,
            ]),
        ];
    }
}
