<?php

use App\Models\Employee;
use App\Models\User;

beforeEach(function () {
    Employee::query()->delete();
});

it('returns paginated employees data', function () {
    Employee::factory(25)->create();

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?page=1&per_page=10')
        ->assertSuccessful()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 10)
        ->assertJsonPath('meta.total', 25)
        ->assertJsonPath('meta.last_page', 3);
});

it('searches employees by name', function () {
    Employee::factory()->create(['name' => 'John Doe']);
    Employee::factory()->create(['name' => 'Jane Smith']);
    Employee::factory()->create(['name' => 'Bob Wilson']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?search=John')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'John Doe');
});

it('searches employees by email', function () {
    Employee::factory()->create(['email' => 'john@example.com']);
    Employee::factory()->create(['email' => 'jane@example.com']);
    Employee::factory()->create(['email' => 'bob@test.com']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?search=example')
        ->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

it('searches employees by department', function () {
    Employee::factory()->create(['department' => 'Engineering']);
    Employee::factory()->create(['department' => 'Engineering']);
    Employee::factory()->create(['department' => 'Marketing']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?search=Engineering')
        ->assertSuccessful()
        ->assertJsonCount(2, 'data');
});

it('sorts employees by name ascending', function () {
    Employee::factory()->create(['name' => 'Zoe']);
    Employee::factory()->create(['name' => 'Alice']);
    Employee::factory()->create(['name' => 'Bob']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?sort_by=name&sort_direction=asc')
        ->assertSuccessful()
        ->assertJsonPath('data.0.name', 'Alice')
        ->assertJsonPath('data.1.name', 'Bob')
        ->assertJsonPath('data.2.name', 'Zoe');
});

it('sorts employees by name descending', function () {
    Employee::factory()->create(['name' => 'Zoe']);
    Employee::factory()->create(['name' => 'Alice']);
    Employee::factory()->create(['name' => 'Bob']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?sort_by=name&sort_direction=desc')
        ->assertSuccessful()
        ->assertJsonPath('data.0.name', 'Zoe')
        ->assertJsonPath('data.1.name', 'Bob')
        ->assertJsonPath('data.2.name', 'Alice');
});

it('sorts employees by salary', function () {
    Employee::factory()->create(['salary' => 50000]);
    Employee::factory()->create(['salary' => 100000]);
    Employee::factory()->create(['salary' => 75000]);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?sort_by=salary&sort_direction=desc')
        ->assertSuccessful()
        ->assertJsonPath('data.0.salary', 100000)
        ->assertJsonPath('data.1.salary', 75000)
        ->assertJsonPath('data.2.salary', 50000);
});

it('sorts employees by start_date', function () {
    Employee::factory()->create(['start_date' => '2020-01-01']);
    Employee::factory()->create(['start_date' => '2023-01-01']);
    Employee::factory()->create(['start_date' => '2021-01-01']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?sort_by=start_date&sort_direction=asc')
        ->assertSuccessful()
        ->assertJsonPath('data.0.start_date', '2020-01-01T00:00:00.000000Z')
        ->assertJsonPath('data.1.start_date', '2021-01-01T00:00:00.000000Z')
        ->assertJsonPath('data.2.start_date', '2023-01-01T00:00:00.000000Z');
});

it('validates sort_by parameter', function () {
    Employee::factory(5)->create();

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?sort_by=invalid_column')
        ->assertSessionHasErrors(['sort_by']);
});

it('validates per_page maximum', function () {
    Employee::factory(150)->create();

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?per_page=150')
        ->assertSessionHasErrors(['per_page']);
});

it('requires authentication', function () {
    Employee::factory(5)->create();

    $this->get('/api/employees')
        ->assertRedirect('/login');
});

it('returns empty array when no employees match search', function () {
    Employee::factory()->create(['name' => 'John Doe']);
    Employee::factory()->create(['name' => 'Jane Smith']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?search=NonExistent')
        ->assertSuccessful()
        ->assertJsonCount(0, 'data')
        ->assertJsonPath('meta.total', 0);
});

it('combines search and pagination', function () {
    Employee::factory()->create(['name' => 'John Smith', 'department' => 'Engineering']);
    Employee::factory()->create(['name' => 'John Doe', 'department' => 'Marketing']);
    Employee::factory()->create(['name' => 'Jane Smith', 'department' => 'Engineering']);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/api/employees?search=John&per_page=1&page=1')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('meta.total', 2);
});
