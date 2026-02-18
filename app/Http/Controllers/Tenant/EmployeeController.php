<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\EmployeeStoreRequest;
use App\Http\Requests\Tenant\EmployeeUpdateRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Services\EmployeeLimitService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeLimitService $limitService
    ) {}

    public function index(): Response
    {
        $employees = Employee::with('department')
            ->orderBy('name')
            ->paginate(10);

        $limitInfo = $this->limitService->getPlanLimitInfo();

        return Inertia::render('Tenant/Employees/Index', [
            'employees' => $employees,
            'limitInfo' => $limitInfo,
        ]);
    }

    public function create(): Response
    {
        if (! $this->limitService->canAddEmployee()) {
            return redirect()->route('tenant.employees.index')
                ->with('error', 'Employee limit reached. Upgrade to Pro for unlimited employees.');
        }

        $departments = Department::orderBy('name')->get();

        return Inertia::render('Tenant/Employees/Create', [
            'departments' => $departments,
        ]);
    }

    public function store(EmployeeStoreRequest $request): RedirectResponse
    {
        if (! $this->limitService->canAddEmployee()) {
            return redirect()->route('tenant.employees.index')
                ->with('error', 'Employee limit reached. Upgrade to Pro for unlimited employees.');
        }

        Employee::create($request->validated());

        return redirect()->route('tenant.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee): Response
    {
        $departments = Department::orderBy('name')->get();

        return Inertia::render('Tenant/Employees/Edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(EmployeeUpdateRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('tenant.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('tenant.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
