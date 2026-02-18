<?php

declare(strict_types=1);

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
    // Tenant routes will be added here
    // For now, we don't have a root route for tenants
    // The home page is handled by the central domain

    // Tenant dashboard route (to be implemented)
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
