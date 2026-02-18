<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayrollPeriod>
 */
class PayrollPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $month = fake()->numberBetween(1, 12);
        $year = fake()->numberBetween(2024, 2026);
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'month' => $month,
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_processed' => false,
            'processed_at' => null,
        ];
    }

    /**
     * Indicate that the payroll period is processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Create a period for a specific month and year.
     */
    public function forMonth(int $month, int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'month' => $month,
            'year' => $year,
            'start_date' => now()->setYear($year)->setMonth($month)->startOfMonth(),
            'end_date' => now()->setYear($year)->setMonth($month)->endOfMonth(),
        ]);
    }
}
