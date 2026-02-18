<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveQuota;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    /**
     * Calculate the number of working days between two dates.
     * Excludes weekends (Saturday and Sunday).
     */
    public function calculateDaysCount(string $startDate, string $endDat): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDat);

        $days = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (! $current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * Get or create leave quota for an employee for a specific year.
     */
    public function getOrCreateLeaveQuota(Employee $employee, int $year): LeaveQuota
    {
        return LeaveQuota::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'year' => $year,
            ],
            [
                'total_days' => 12,
                'used_days' => 0,
            ]
        );
    }

    /**
     * Check if an employee can request leave for the specified days.
     */
    public function canRequestLeave(Employee $employee, int $days, ?int $year = null): bool
    {
        $year = $year ?? now()->year;
        $quota = $this->getOrCreateLeaveQuota($employee, $year);

        return $quota->hasEnoughDays($days);
    }

    /**
     * Approve a leave request.
     */
    public function approveLeaveRequest(LeaveRequest $leaveRequest): bool
    {
        if (! $leaveRequest->isPending()) {
            return false;
        }

        return DB::transaction(function () use ($leaveRequest): bool {
            $year = $leaveRequest->start_date->year;
            $quota = $this->getOrCreateLeaveQuota($leaveRequest->employee, $year);

            // Check if employee has enough leave days
            if (! $quota->hasEnoughDays($leaveRequest->days_count)) {
                return false;
            }

            // Update quota
            $quota->used_days += $leaveRequest->days_count;
            $quota->save();

            // Update leave request
            $leaveRequest->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Reject a leave request.
     */
    public function rejectLeaveRequest(LeaveRequest $leaveRequest, ?string $notes = null): bool
    {
        if (! $leaveRequest->isPending()) {
            return false;
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'notes' => $notes,
            'processed_at' => now(),
        ]);

        return true;
    }

    /**
     * Calculate leave deduction for an employee in a specific month/year.
     * Deduction is calculated for excess leave days beyond the quota.
     */
    public function calculateLeaveDeduction(Employee $employee, int $year, int $month): int
    {
        $quota = LeaveQuota::where('employee_id', $employee->id)
            ->where('year', $year)
            ->first();

        if ($quota === null || $quota->used_days <= $quota->total_days) {
            return 0;
        }

        // Calculate excess days
        $excessDays = $quota->used_days - $quota->total_days;

        // Calculate daily rate (22 working days per month)
        $dailyRate = (int) round($employee->base_salary / 22);

        return $excessDays * $dailyRate;
    }

    /**
     * Get leave statistics for an employee.
     *
     * @return array<string, mixed>
     */
    public function getLeaveStats(Employee $employee, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $quota = $this->getOrCreateLeaveQuota($employee, $year);

        $pendingRequests = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        $approvedRequests = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->count();

        return [
            'total_days' => $quota->total_days,
            'used_days' => $quota->used_days,
            'remaining_days' => $quota->getRemainingDays(),
            'pending_requests' => $pendingRequests,
            'approved_requests' => $approvedRequests,
        ];
    }
}
