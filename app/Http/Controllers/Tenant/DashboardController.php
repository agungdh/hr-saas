<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Services\EmployeeLimitService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected EmployeeLimitService $limitService
    ) {}

    public function index(): Response
    {
        $employeeCount = Employee::count();
        $activeEmployeeCount = Employee::where('status', 'active')->count();
        $pendingLeaveRequests = LeaveRequest::where('status', 'pending')->count();
        $unprocessedPayrolls = PayrollPeriod::where('is_processed', false)->count();

        $limitInfo = $this->limitService->getPlanLimitInfo();

        $recentLeaveRequests = LeaveRequest::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Tenant/Dashboard', [
            'stats' => [
                'total_employees' => $employeeCount,
                'active_employees' => $activeEmployeeCount,
                'pending_leave_requests' => $pendingLeaveRequests,
                'unprocessed_payrolls' => $unprocessedPayrolls,
            ],
            'limitInfo' => $limitInfo,
            'recentLeaveRequests' => $recentLeaveRequests,
        ]);
    }
}
