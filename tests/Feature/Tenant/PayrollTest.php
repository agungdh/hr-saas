<?php

use App\Models\Employee;
use App\Models\LeaveQuota;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PayrollService;
use Stancl\Tenancy\Facades\Tenancy;

beforeEach(function () {
    $this->tenant = Tenant::create([
        'id' => 'test-company',
        'name' => 'Test Company',
        'slug' => 'test-company',
        'plan' => 'pro',
    ]);

    $this->tenant->domains()->create(['domain' => 'test-company.localhost']);

    Tenancy::initialize($this->tenant);

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@test-company.localhost',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($this->user);
});

afterEach(function () {
    Tenancy::end();
});

it('can list payroll periods', function () {
    PayrollPeriod::factory()->count(3)->create();

    $response = $this->get('/payroll');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('periods.data', 3)
    );
});

it('can show payroll period details', function () {
    $period = PayrollPeriod::factory()->processed()->create();
    $employee = Employee::factory()->create();
    Payroll::factory()->for($employee)->for($period)->create();

    $response = $this->get("/payroll/{$period->id}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('period')
        ->has('payrolls')
        ->has('stats')
    );
});

it('can generate payroll for a new period', function () {
    Employee::factory()->active()->count(3)->create();

    $response = $this->post('/payroll/generate', [
        'month' => 2,
        'year' => 2026,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('payroll_periods', [
        'month' => 2,
        'year' => 2026,
        'is_processed' => true,
    ]);

    $period = PayrollPeriod::where('month', 2)->where('year', 2026)->first();
    expect($period->payrolls)->toHaveCount(3);
});

it('cannot generate payroll for already processed period', function () {
    PayrollPeriod::factory()->processed()->forMonth(2, 2026)->create();
    Employee::factory()->active()->count(3)->create();

    $response = $this->post('/payroll/generate', [
        'month' => 2,
        'year' => 2026,
    ]);

    $response->assertRedirect('/payroll');
    $response->assertSessionHas('error');
});

it('calculates net salary correctly', function () {
    $service = app(PayrollService::class);

    // 10M - 500K (tax) - 0 (leave) + 0 (allowances) = 9.5M
    $netSalary = $service->calculateNetSalary(10000000, 500000, 0, 0);

    expect($netSalary)->toBe(9500000);
});

it('calculates tax deduction correctly', function () {
    $service = app(PayrollService::class);

    // 5% of 10M = 500K
    $taxDeduction = $service->calculateTaxDeduction(10000000);

    expect($taxDeduction)->toBe(500000);
});

it('generates payroll with correct calculations', function () {
    $employee = Employee::factory()->active()->create(['base_salary' => 10000000]);
    LeaveQuota::factory()->for($employee)->unused()->create();
    $period = PayrollPeriod::factory()->forMonth(2, 2026)->create();

    $service = app(PayrollService::class);
    $payroll = $service->generatePayroll($employee, $period);

    expect($payroll->base_salary)->toBe(10000000);
    expect($payroll->tax_deduction)->toBe(500000);
    expect($payroll->leave_deduction)->toBe(0);
    expect($payroll->net_salary)->toBe(9500000);
});

it('calculates leave deduction for excess leave', function () {
    $employee = Employee::factory()->create(['base_salary' => 11000000]); // Daily rate = 500K
    LeaveQuota::factory()->for($employee)->create([
        'total_days' => 12,
        'used_days' => 15, // 3 excess days
    ]);

    $service = app(PayrollService::class);
    $deduction = $service->leaveService->calculateLeaveDeduction($employee, now()->year, now()->month);

    // 3 excess days * 500K daily rate = 1.5M
    expect($deduction)->toBe(1500000);
});
