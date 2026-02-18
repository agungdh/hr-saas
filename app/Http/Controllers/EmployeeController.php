<?php

namespace App\Http\Controllers;

use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Display the table demo with employees.
     */
    public function index(): \Inertia\Response
    {
        return inertia('TableDemo', [
            'employees' => Employee::all(),
        ]);
    }
}
