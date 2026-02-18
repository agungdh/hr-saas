<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DepartmentStoreRequest;
use App\Http\Requests\Tenant\DepartmentUpdateRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(): Response
    {
        $departments = Department::withCount('employees')
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('Tenant/Departments/Index', [
            'departments' => $departments,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Departments/Create');
    }

    public function store(DepartmentStoreRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()->route('tenant.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department): Response
    {
        return Inertia::render('Tenant/Departments/Edit', [
            'department' => $department,
        ]);
    }

    public function update(DepartmentUpdateRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('tenant.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->count() > 0) {
            return redirect()->route('tenant.departments.index')
                ->with('error', 'Cannot delete department with existing employees.');
        }

        $department->delete();

        return redirect()->route('tenant.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
