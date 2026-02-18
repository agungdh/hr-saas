<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
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

it('can list employees', function () {
    Employee::factory()->count(3)->create();

    $response = $this->get('/employees');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('employees.data', 3)
        ->has('limitInfo')
    );
});

it('can create an employee when under limit', function () {
    $department = Department::factory()->create();

    $response = $this->post('/employees', [
        'department_id' => $department->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'position' => 'Software Engineer',
        'status' => 'active',
        'base_salary' => 10000000,
        'start_date' => '2026-01-01',
    ]);

    $response->assertRedirect('/employees');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('employees', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('cannot create employee when limit is reached on free plan', function () {
    Employee::factory()->count(5)->create();

    $response = $this->post('/employees', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'position' => 'Software Engineer',
        'status' => 'active',
        'base_salary' => 10000000,
        'start_date' => '2026-01-01',
    ]);

    $response->assertRedirect('/employees');
    $response->assertSessionHas('error');
});

it('can create unlimited employees on pro plan', function () {
    $this->tenant->update(['plan' => 'pro']);
    Tenancy::end();
    Tenancy::initialize($this->tenant);

    Employee::factory()->count(10)->create();

    $response = $this->post('/employees', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'position' => 'Software Engineer',
        'status' => 'active',
        'base_salary' => 10000000,
        'start_date' => '2026-01-01',
    ]);

    $response->assertRedirect('/employees');
    $response->assertSessionHas('success');
});

it('validates required fields when creating employee', function () {
    $response = $this->post('/employees', []);

    $response->assertSessionHasErrors(['name', 'email', 'position', 'status', 'base_salary', 'start_date']);
});

it('validates unique email', function () {
    Employee::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post('/employees', [
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'position' => 'Software Engineer',
        'status' => 'active',
        'base_salary' => 10000000,
        'start_date' => '2026-01-01',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('can update an employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->put("/employees/{$employee->id}", [
        'department_id' => null,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'position' => 'Senior Engineer',
        'status' => 'active',
        'base_salary' => 15000000,
        'start_date' => '2026-01-01',
    ]);

    $response->assertRedirect('/employees');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

it('can delete an employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->delete("/employees/{$employee->id}");

    $response->assertRedirect('/employees');
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('employees', [
        'id' => $employee->id,
    ]);
});
