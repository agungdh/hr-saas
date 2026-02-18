<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;

class PayrollService
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    /**
     * Calculate net salary.
     * net_salary = base_salary - tax_deduction - leave_deduction + allowances
     */
    public function calculateNetSalary(int $baseSalary, int $taxDeduction, int $leaveDeduction, int $allowances): int
    {
        return max(0, $baseSalary - $taxDeduction - $leaveDeduction + $allowances);
    }

    /**
     * Calculate tax deduction based on salary.
     * Progressive tax rates for Indonesia (simplified).
     */
    public function calculateTaxDeduction(int $baseSalary): int
    {
        // Simplified tax calculation (5% of base salary)
        // In real implementation, this would use progressive tax brackets
        return (int) round($baseSalary * 0.05);
    }

    /**
     * Generate payroll for a specific employee and period.
     */
    public function generatePayroll(Employee $employee, PayrollPeriod $period): Payroll
    {
        // Check if payroll already exists
        $existingPayroll = Payroll::where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->first();

        if ($existingPayroll !== null) {
            return $existingPayroll;
        }

        // Calculate deductions and allowances
        $taxDeduction = $this->calculateTaxDeduction($employee->base_salary);
        $leaveDeduction = $this->leaveService->calculateLeaveDeduction(
            $employee,
            $period->year,
            $period->month
        );
        $allowances = 0; // Can be extended for meal allowance, transport, etc.

        $netSalary = $this->calculateNetSalary(
            $employee->base_salary,
            $taxDeduction,
            $leaveDeduction,
            $allowances
        );

        return Payroll::create([
            'employee_id' => $employee->id,
            'payroll_period_id' => $period->id,
            'base_salary' => $employee->base_salary,
            'tax_deduction' => $taxDeduction,
            'leave_deduction' => $leaveDeduction,
            'allowances' => $allowances,
            'net_salary' => $netSalary,
        ]);
    }

    /**
     * Generate payrolls for all active employees in a period.
     * Returns the count of generated payrolls.
     */
    public function generatePayrollForPeriod(PayrollPeriod $period): int
    {
        $employees = Employee::where('status', 'active')->get();
        $count = 0;

        foreach ($employees as $employee) {
            $this->generatePayroll($employee, $period);
            $count++;
        }

        // Mark period as processed
        $period->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);

        return $count;
    }

    /**
     * Create a payroll period for a specific month and year.
     */
    public function createPayrollPeriod(int $month, int $year): PayrollPeriod
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return PayrollPeriod::create([
            'month' => $month,
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_processed' => false,
        ]);
    }

    /**
     * Get payroll statistics for a period.
     *
     * @return array<string, mixed>
     */
    public function getPayrollStats(PayrollPeriod $period): array
    {
        $payrolls = $period->payrolls;

        return [
            'total_employees' => $payrolls->count(),
            'total_base_salary' => $payrolls->sum('base_salary'),
            'total_tax_deduction' => $payrolls->sum('tax_deduction'),
            'total_leave_deduction' => $payrolls->sum('leave_deduction'),
            'total_allowances' => $payrolls->sum('allowances'),
            'total_net_salary' => $payrolls->sum('net_salary'),
        ];
    }
}
