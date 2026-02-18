<?php

namespace Database\Factories;

use App\Models\Department;
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
            'department_id' => Department::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'position' => fake()->jobTitle(),
            'status' => fake()->randomElement(['active', 'inactive', 'on-leave']),
            'base_salary' => fake()->numberBetween(5000000, 50000000),
            'start_date' => fake()->date('-2 years'),
        ];
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the employee has no department.
     */
    public function withoutDepartment(): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => null,
        ]);
    }
}
