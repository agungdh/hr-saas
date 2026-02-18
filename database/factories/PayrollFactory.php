<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseSalary = fake()->numberBetween(5000000, 50000000);
        $taxDeduction = (int) round($baseSalary * 0.05);
        $leaveDeduction = fake()->numberBetween(0, 2000000);
        $allowances = fake()->numberBetween(0, 1000000);
        $netSalary = max(0, $baseSalary - $taxDeduction - $leaveDeduction + $allowances);

        return [
            'employee_id' => Employee::factory(),
            'payroll_period_id' => PayrollPeriod::factory(),
            'base_salary' => $baseSalary,
            'tax_deduction' => $taxDeduction,
            'leave_deduction' => $leaveDeduction,
            'allowances' => $allowances,
            'net_salary' => $netSalary,
            'notes' => null,
        ];
    }

    /**
     * Create payroll for a specific employee and period.
     */
    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employee->id,
            'base_salary' => $employee->base_salary,
        ]);
    }

    /**
     * Create payroll with no deductions.
     */
    public function noDeductions(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_deduction' => 0,
            'leave_deduction' => 0,
            'net_salary' => $attributes['base_salary'] + ($attributes['allowances'] ?? 0),
        ]);
    }
}
