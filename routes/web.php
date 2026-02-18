<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('table-demo', [EmployeeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('table-demo');

Route::get('api/employees', [EmployeeController::class, 'data'])
    ->middleware(['auth', 'verified'])
    ->name('employees.data');

require __DIR__.'/settings.php';
