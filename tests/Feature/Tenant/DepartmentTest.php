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

it('can list departments', function () {
    Department::factory()->count(3)->create();

    $response = $this->get('/departments');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('departments.data', 3)
    );
});

it('can create a department', function () {
    $response = $this->post('/departments', [
        'name' => 'Engineering',
        'description' => 'Software Engineering Department',
    ]);

    $response->assertRedirect('/departments');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('departments', [
        'name' => 'Engineering',
        'description' => 'Software Engineering Department',
    ]);
});

it('validates required fields when creating department', function () {
    $response = $this->post('/departments', []);

    $response->assertSessionHasErrors(['name']);
});

it('can update a department', function () {
    $department = Department::factory()->create();

    $response = $this->put("/departments/{$department->id}", [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);

    $response->assertRedirect('/departments');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);
});

it('can delete a department without employees', function () {
    $department = Department::factory()->create();

    $response = $this->delete("/departments/{$department->id}");

    $response->assertRedirect('/departments');
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('departments', [
        'id' => $department->id,
    ]);
});

it('cannot delete a department with employees', function () {
    $department = Department::factory()->create();
    Employee::factory()->for($department)->create();

    $response = $this->delete("/departments/{$department->id}");

    $response->assertRedirect('/departments');
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
    ]);
});
