<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeIndexRequest;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Display the table demo with employees.
     */
    public function index(): \Inertia\Response
    {
        return inertia('TableDemo');
    }

    /**
     * Get paginated employees with search and sorting.
     */
    public function data(EmployeeIndexRequest $request): \Illuminate\Http\JsonResponse
    {
        $query = Employee::query();

        // Search across multiple columns
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereAny([
                'name',
                'email',
                'department',
                'position',
            ], 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Map column names to database columns
        $columnMap = [
            'start_date' => 'start_date',
        ];

        $dbColumn = $columnMap[$sortBy] ?? $sortBy;
        $query->orderBy($dbColumn, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $employees = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $employees->items(),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'from' => $employees->firstItem(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'to' => $employees->lastItem(),
                'total' => $employees->total(),
            ],
        ]);
    }
}
