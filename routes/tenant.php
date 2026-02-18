<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\DepartmentController;
use App\Http\Controllers\Tenant\EmployeeController;
use App\Http\Controllers\Tenant\LeaveController;
use App\Http\Controllers\Tenant\PayrollController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
*/

Route::middleware([
    'web',
])->group(function (): void {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

    // Departments
    Route::resource('departments', DepartmentController::class, [
        'as' => 'tenant',
    ]);

    // Employees
    Route::resource('employees', EmployeeController::class, [
        'as' => 'tenant',
    ]);

    // Leave Management
    Route::prefix('leave')->name('tenant.leave.')->group(function (): void {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::post('/{leaveRequest}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::post('/{leaveRequest}/reject', [LeaveController::class, 'reject'])->name('reject');
    });

    // Payroll Management
    Route::prefix('payroll')->name('tenant.payroll.')->group(function (): void {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/{period}', [PayrollController::class, 'show'])->name('show');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
    });
});
