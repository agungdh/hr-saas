<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveQuota>
 */
class LeaveQuotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'year' => now()->year,
            'total_days' => 12,
            'used_days' => fake()->numberBetween(0, 12),
        ];
    }

    /**
     * Indicate that no leave has been used.
     */
    public function unused(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_days' => 0,
        ]);
    }

    /**
     * Indicate that all leave has been used.
     */
    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_days' => 12,
        ]);
    }
}
