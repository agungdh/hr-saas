<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\LeaveRequestStoreRequest;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function index(Request $request): Response
    {
        $query = LeaveRequest::with('employee.department');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return Inertia::render('Tenant/Leave/Index', [
            'leaveRequests' => $leaveRequests,
            'filters' => [
                'status' => $request->status ?? 'all',
            ],
        ]);
    }

    public function create(): Response
    {
        $employees = Employee::with('department')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return Inertia::render('Tenant/Leave/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(LeaveRequestStoreRequest $request): RedirectResponse
    {
        $daysCount = $this->leaveService->calculateDaysCount(
            $request->start_date,
            $request->end_date
        );

        $employee = Employee::find($request->employee_id);
        $year = now()->parse($request->start_date)->year;

        if (! $this->leaveService->canRequestLeave($employee, $daysCount, $year)) {
            return redirect()->route('tenant.leave.index')
                ->with('error', 'Employee does not have enough leave days remaining.');
        }

        LeaveRequest::create([
            ...$request->validated(),
            'days_count' => $daysCount,
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.leave.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function approve(LeaveRequest $leaveRequest): RedirectResponse
    {
        if (! $leaveRequest->isPending()) {
            return redirect()->route('tenant.leave.index')
                ->with('error', 'This leave request has already been processed.');
        }

        $success = $this->leaveService->approveLeaveRequest($leaveRequest);

        if (! $success) {
            return redirect()->route('tenant.leave.index')
                ->with('error', 'Could not approve leave request. Employee may not have enough leave days.');
        }

        return redirect()->route('tenant.leave.index')
            ->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if (! $leaveRequest->isPending()) {
            return redirect()->route('tenant.leave.index')
                ->with('error', 'This leave request has already been processed.');
        }

        $this->leaveService->rejectLeaveRequest(
            $leaveRequest,
            $request->input('notes')
        );

        return redirect()->route('tenant.leave.index')
            ->with('success', 'Leave request rejected.');
    }
}
