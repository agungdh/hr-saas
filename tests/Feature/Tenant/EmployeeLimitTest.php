<?php

use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use App\Services\EmployeeLimitService;
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

it('returns 5 as limit for free plan', function () {
    $service = new EmployeeLimitService;

    expect($service->getLimit())->toBe(5);
});

it('returns unlimited for pro plan', function () {
    $this->tenant->update(['plan' => 'pro']);
    Tenancy::end();
    Tenancy::initialize($this->tenant);

    $service = new EmployeeLimitService;

    expect($service->getLimit())->toBe(PHP_INT_MAX);
});

it('can add employee when under limit', function () {
    Employee::factory()->count(3)->create();

    $service = new EmployeeLimitService;

    expect($service->canAddEmployee())->toBeTrue();
    expect($service->isLimitExceeded())->toBeFalse();
});

it('cannot add employee when at limit', function () {
    Employee::factory()->count(5)->create();

    $service = new EmployeeLimitService;

    expect($service->canAddEmployee())->toBeFalse();
    expect($service->isLimitExceeded())->toBeTrue();
});

it('pro plan allows unlimited employees', function () {
    $this->tenant->update(['plan' => 'pro']);
    Tenancy::end();
    Tenancy::initialize($this->tenant);

    Employee::factory()->count(10)->create();

    $service = new EmployeeLimitService;

    expect($service->canAddEmployee())->toBeTrue();
});

it('returns correct plan limit info for free plan', function () {
    Employee::factory()->count(3)->create();

    $service = new EmployeeLimitService;
    $info = $service->getPlanLimitInfo();

    expect($info['plan'])->toBe('free');
    expect($info['current_count'])->toBe(3);
    expect($info['limit'])->toBe(5);
    expect($info['remaining'])->toBe(2);
    expect($info['is_unlimited'])->toBeFalse();
    expect($info['can_add_more'])->toBeTrue();
});

it('returns correct plan limit info when limit reached', function () {
    Employee::factory()->count(5)->create();

    $service = new EmployeeLimitService;
    $info = $service->getPlanLimitInfo();

    expect($info['remaining'])->toBe(0);
    expect($info['can_add_more'])->toBeFalse();
});
