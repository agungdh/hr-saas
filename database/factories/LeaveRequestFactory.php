<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $daysCount = fake()->numberBetween(1, 5);

        return [
            'employee_id' => Employee::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime($startDate->format('Y-m-d')." +{$daysCount} days")),
            'days_count' => $daysCount,
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'notes' => null,
            'processed_at' => null,
        ];
    }

    /**
     * Indicate that the leave request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
        ]);
    }

    /**
     * Indicate that the leave request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the leave request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'notes' => fake()->sentence(),
            'processed_at' => now(),
        ]);
    }
}
