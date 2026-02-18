<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Tenant;

class EmployeeLimitService
{
    /**
     * Get the employee limit for the current tenant.
     * Free plan: 5 employees
     * Pro plan: unlimited (returns PHP_INT_MAX)
     */
    public function getLimit(): int
    {
        $tenant = tenant();

        if ($tenant === null) {
            return PHP_INT_MAX;
        }

        if ($tenant->plan === 'pro') {
            return PHP_INT_MAX;
        }

        return 5;
    }

    /**
     * Check if the tenant can add a new employee.
     */
    public function canAddEmployee(): bool
    {
        $currentCount = Employee::count();
        $limit = $this->getLimit();

        return $currentCount < $limit;
    }

    /**
     * Check if the tenant has reached the employee limit.
     */
    public function isLimitExceeded(): bool
    {
        return ! $this->canAddEmployee();
    }

    /**
     * Get the current employee count.
     */
    public function getCurrentCount(): int
    {
        return Employee::count();
    }

    /**
     * Get plan limit information.
     *
     * @return array<string, mixed>
     */
    public function getPlanLimitInfo(): array
    {
        $tenant = tenant();
        $currentCount = $this->getCurrentCount();
        $limit = $this->getLimit();

        return [
            'plan' => $tenant?->plan ?? 'free',
            'current_count' => $currentCount,
            'limit' => $limit,
            'remaining' => $limit === PHP_INT_MAX ? 'unlimited' : max(0, $limit - $currentCount),
            'is_unlimited' => $limit === PHP_INT_MAX,
            'can_add_more' => $this->canAddEmployee(),
        ];
    }
}
