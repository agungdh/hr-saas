<?php

use App\Models\Employee;
use App\Models\LeaveQuota;
use App\Models\LeaveRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LeaveService;
use Stancl\Tenancy\Facades\Tenancy;

beforeEach(function () {
    $this->tenant = Tenant::create([
        'id' => 'test-company',
        'name' => 'Test Company',
        'slug' => 'test-company',
        'plan' => 'free',
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

it('can list leave requests', function () {
    $employee = Employee::factory()->create();
    LeaveRequest::factory()->for($employee)->count(3)->create();

    $response = $this->get('/leave');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('leaveRequests.data', 3)
    );
});

it('can filter leave requests by status', function () {
    $employee = Employee::factory()->create();
    LeaveRequest::factory()->for($employee)->pending()->count(2)->create();
    LeaveRequest::factory()->for($employee)->approved()->count(1)->create();

    $response = $this->get('/leave?status=pending');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('leaveRequests.data', 2)
    );
});

it('can create a leave request', function () {
    $employee = Employee::factory()->create();
    LeaveQuota::factory()->for($employee)->unused()->create();

    $response = $this->post('/leave', [
        'employee_id' => $employee->id,
        'start_date' => now()->addDays(1)->format('Y-m-d'),
        'end_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Family vacation',
    ]);

    $response->assertRedirect('/leave');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('leave_requests', [
        'employee_id' => $employee->id,
        'reason' => 'Family vacation',
        'status' => 'pending',
    ]);
});

it('cannot create leave request when quota is exhausted', function () {
    $employee = Employee::factory()->create();
    LeaveQuota::factory()->for($employee)->exhausted()->create();

    $response = $this->post('/leave', [
        'employee_id' => $employee->id,
        'start_date' => now()->addDays(1)->format('Y-m-d'),
        'end_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Family vacation',
    ]);

    $response->assertRedirect('/leave');
    $response->assertSessionHas('error');
});

it('can approve a leave request', function () {
    $employee = Employee::factory()->create();
    $quota = LeaveQuota::factory()->for($employee)->unused()->create();
    $leaveRequest = LeaveRequest::factory()->for($employee)->pending()->create(['days_count' => 3]);

    $response = $this->post("/leave/{$leaveRequest->id}/approve");

    $response->assertRedirect('/leave');
    $response->assertSessionHas('success');

    $leaveRequest->refresh();
    $quota->refresh();

    expect($leaveRequest->status)->toBe('approved');
    expect($quota->used_days)->toBe(3);
});

it('can reject a leave request', function () {
    $employee = Employee::factory()->create();
    $leaveRequest = LeaveRequest::factory()->for($employee)->pending()->create();

    $response = $this->post("/leave/{$leaveRequest->id}/reject", [
        'notes' => 'Not enough coverage',
    ]);

    $response->assertRedirect('/leave');
    $response->assertSessionHas('success');

    $leaveRequest->refresh();

    expect($leaveRequest->status)->toBe('rejected');
    expect($leaveRequest->notes)->toBe('Not enough coverage');
});

it('cannot approve already processed request', function () {
    $employee = Employee::factory()->create();
    $leaveRequest = LeaveRequest::factory()->for($employee)->approved()->create();

    $response = $this->post("/leave/{$leaveRequest->id}/approve");

    $response->assertRedirect('/leave');
    $response->assertSessionHas('error');
});

it('calculates working days correctly', function () {
    $service = new LeaveService;

    // Monday to Friday (5 working days)
    $days = $service->calculateDaysCount('2026-02-16', '2026-02-20');
    expect($days)->toBe(5);

    // Monday to Sunday (5 working days - excludes weekend)
    $days = $service->calculateDaysCount('2026-02-16', '2026-02-22');
    expect($days)->toBe(5);
});
