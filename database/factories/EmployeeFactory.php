<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'department' => fake()->randomElement(['Engineering', 'Marketing', 'HR', 'Finance', 'Sales', 'Operations', 'Design']),
            'position' => fake()->jobTitle(),
            'status' => fake()->randomElement(['active', 'inactive', 'on-leave']),
            'salary' => fake()->numberBetween(50000, 150000),
            'start_date' => fake()->date(),
        ];
    }
}
