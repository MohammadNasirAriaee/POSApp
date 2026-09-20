<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_employee_index_page_with_stats(): void
    {
        Employee::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'position' => 'Store Manager',
            'salary' => 5000,
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $response = $this->get(route('employees.index'));

        $response->assertStatus(200);
        $response->assertSee('Employee Directory');
        $response->assertSee('Alice Smith');
        $response->assertSee('Store Manager');
    }

    public function test_row_actions_carry_accessible_names(): void
    {
        Employee::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $this->get(route('employees.index'))
            ->assertOk()
            ->assertSee('aria-label="View profile for Alice Smith"', false)
            ->assertSee('aria-label="Edit Alice Smith"', false)
            ->assertSee('aria-label="Delete Alice Smith"', false)
            ->assertSee('aria-label="Change status for Alice Smith, currently Active"', false);
    }

    public function test_can_search_employees_by_keyword(): void
    {
        Employee::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        Employee::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Miller',
            'email' => 'jane.miller@example.com',
        ]);

        $response = $this->get(route('employees.index', ['search' => 'Miller']));

        $response->assertStatus(200);
        $response->assertSee('Jane Miller');
        $response->assertDontSee('John Doe');
    }

    public function test_can_filter_employees_by_position_and_status(): void
    {
        Employee::factory()->create([
            'first_name' => 'Robert',
            'last_name' => 'Brown',
            'position' => 'Cashier',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        Employee::factory()->create([
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'position' => 'Store Manager',
            'status' => Employee::STATUS_ON_LEAVE,
        ]);

        $response = $this->get(route('employees.index', [
            'position' => 'Cashier',
            'status' => 'active',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Robert Brown');
        $response->assertDontSee('Emily Davis');
    }

    public function test_can_create_a_new_employee(): void
    {
        $payload = [
            'first_name' => 'Michael',
            'last_name' => 'Jordan',
            'email' => 'michael.j@example.com',
            'phone' => '+1555123456',
            'address' => '123 Main St',
            'position' => 'Sales Associate',
            'salary' => 3500,
            'hire_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ];

        $response = $this->post(route('employees.store'), $payload);

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'email' => 'michael.j@example.com',
            'first_name' => 'Michael',
        ]);
    }

    public function test_can_show_employee_profile(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'David',
            'last_name' => 'Beckham',
            'position' => 'Assistant Manager',
        ]);

        $response = $this->get(route('employees.show', $employee));

        $response->assertStatus(200);
        $response->assertSee('David Beckham');
        $response->assertSee('Assistant Manager');
    }

    public function test_can_update_employee_details(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Sam',
            'last_name' => 'Wilson',
            'email' => 'sam.w@example.com',
        ]);

        $payload = [
            'first_name' => 'Samuel',
            'last_name' => 'Wilson',
            'email' => 'sam.w@example.com',
            'position' => 'Shift Supervisor',
            'salary' => 4200,
            'status' => 'active',
        ];

        $response = $this->put(route('employees.update', $employee), $payload);

        $response->assertRedirect(route('employees.show', $employee));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'first_name' => 'Samuel',
            'position' => 'Shift Supervisor',
        ]);
    }

    public function test_edit_form_prefills_the_existing_hire_date(): void
    {
        $employee = Employee::factory()->create(['hire_date' => '2024-03-15']);

        $this->get(route('employees.edit', $employee))
            ->assertOk()
            ->assertSee('value="2024-03-15"', false);
    }

    public function test_create_form_leaves_the_hire_date_blank(): void
    {
        $this->get(route('employees.create'))
            ->assertOk()
            ->assertSee('id="hire_date" name="hire_date" type="date" value=""', false);
    }

    public function test_can_toggle_employee_status(): void
    {
        $employee = Employee::factory()->create([
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $response = $this->patch(route('employees.toggle-status', $employee));

        $response->assertRedirect();
        $this->assertEquals(Employee::STATUS_ON_LEAVE, $employee->fresh()->status);
    }

    public function test_can_delete_an_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->delete(route('employees.destroy', $employee));

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }
}
