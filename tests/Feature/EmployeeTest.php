<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_position_filter_options_are_alphabetized(): void
    {
        Employee::factory()->create(['position' => 'Store Manager']);
        Employee::factory()->create(['position' => 'Assistant Manager']);
        Employee::factory()->create(['position' => 'Cashier']);

        $positions = $this->get(route('employees.index'))
            ->assertOk()
            ->viewData('positions');

        $this->assertSame(
            ['Assistant Manager', 'Cashier', 'Store Manager'],
            $positions
        );
    }

    public function test_the_position_filter_options_are_alphabetized_case_insensitively(): void
    {
        // A plain orderBy('position') sorts case-sensitively on SQLite, wrongly
        // putting "Cashier" before "banager".
        Employee::factory()->create(['position' => 'banager']);
        Employee::factory()->create(['position' => 'Cashier']);

        $positions = $this->get(route('employees.index'))
            ->assertOk()
            ->viewData('positions');

        $this->assertSame(['banager', 'Cashier'], $positions);
    }

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

    /**
     * The blade template echoes these links through {{ }}, which HTML-escapes
     * the "&" between query params, so a raw route() call won't match.
     */
    private function sortHref(array $params): string
    {
        return 'href="'.e(route('employees.index', $params)).'"';
    }

    public function test_sortable_column_headers_link_to_the_field_and_toggle_direction(): void
    {
        Employee::factory()->create();

        // EmployeeController already validates and applies ?sort=&direction=;
        // the headers just need a way to reach it while carrying the current
        // sort's opposite direction (a second click un-sorts back).
        $this->get(route('employees.index', ['sort' => 'position', 'direction' => 'asc']))
            ->assertOk()
            ->assertSee($this->sortHref(['sort' => 'position', 'direction' => 'desc']), false)
            ->assertSee('Role / Position &uarr;', false);

        $this->get(route('employees.index', ['sort' => 'salary', 'direction' => 'desc']))
            ->assertOk()
            ->assertSee($this->sortHref(['sort' => 'salary', 'direction' => 'asc']), false)
            ->assertSee('Monthly Salary &darr;', false);
    }

    public function test_the_reset_link_appears_once_the_table_is_sorted(): void
    {
        Employee::factory()->create();

        // A non-default sort changes the view just as much as a filter does,
        // but sorting alone previously left no way back to the default view.
        // (The link's text sits on its own line in the template, so the
        // assertion can't require a bare '>Reset<' with no whitespace.)
        $this->get(route('employees.index'))
            ->assertDontSee('Reset');

        $this->get(route('employees.index', ['sort' => 'salary', 'direction' => 'desc']))
            ->assertSee('Reset');
    }

    public function test_sort_links_preserve_the_active_search_and_filters(): void
    {
        Employee::factory()->create(['position' => 'Cashier']);

        $this->get(route('employees.index', ['position' => 'Cashier', 'sort' => 'first_name']))
            ->assertOk()
            ->assertSee(
                $this->sortHref(['position' => 'Cashier', 'sort' => 'status', 'direction' => 'asc']),
                false
            );
    }

    public function test_emails_that_differ_only_by_case_are_rejected_as_duplicates(): void
    {
        Employee::factory()->create(['email' => 'ada@example.com']);

        $this->post(route('employees.store'), [
            'first_name' => 'Someone',
            'last_name' => 'Else',
            'email' => 'ADA@EXAMPLE.COM',
            'position' => 'Cashier',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Employee::count());
    }

    public function test_a_new_employee_email_is_stored_lowercased(): void
    {
        $this->post(route('employees.store'), [
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'Grace.Hopper@Example.COM',
            'position' => 'Cashier',
        ]);

        $this->assertDatabaseHas('employees', ['email' => 'grace.hopper@example.com']);
        $this->assertDatabaseMissing('employees', ['email' => 'Grace.Hopper@Example.COM']);
    }

    public function test_updating_an_employee_still_allows_keeping_its_own_email(): void
    {
        $employee = Employee::factory()->create(['email' => 'ada@example.com']);

        $this->put(route('employees.update', $employee), [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ADA@EXAMPLE.COM',
            'position' => $employee->position,
        ])->assertSessionHasNoErrors();

        $this->assertSame('ada@example.com', $employee->fresh()->email);
    }

    public function test_the_status_filter_marks_the_active_selection(): void
    {
        $this->get(route('employees.index', ['status' => Employee::STATUS_ON_LEAVE]))
            ->assertOk()
            ->assertSee('value="on_leave" selected', false)
            ->assertDontSee('value="active" selected', false)
            ->assertDontSee('value="inactive" selected', false);
    }

    public function test_the_layout_renders_an_error_flash_alongside_success(): void
    {
        // The employee pages render through resources/views/layouts/app.blade.php
        // (Blade, not Inertia), which only ever displayed session('success').
        // Every controller that flashes 'error' elsewhere in the app (category,
        // customer, product, order, POS) would have that message silently
        // dropped here.
        $this->withSession(['error' => 'Something went wrong.'])
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSee('Something went wrong.');
    }

    public function test_filter_controls_carry_accessible_names(): void
    {
        // All three previously relied on a placeholder alone (search) or
        // nothing at all (the two selects) for their accessible name.
        $this->get(route('employees.index'))
            ->assertOk()
            ->assertSee('aria-label="Search employees"', false)
            ->assertSee('aria-label="Filter by role"', false)
            ->assertSee('aria-label="Filter by status"', false);
    }

    public function test_flash_banner_dismiss_buttons_carry_accessible_names(): void
    {
        $this->withSession(['success' => 'Saved.', 'error' => 'Failed.'])
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'id="flash-success-banner"',
                'aria-label="Dismiss"',
                'id="flash-error-banner"',
                'aria-label="Dismiss"',
            ], false);
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

    public function test_the_profile_pages_delete_button_carries_an_accessible_name(): void
    {
        $employee = Employee::factory()->create(['first_name' => 'Alice', 'last_name' => 'Smith']);

        $this->get(route('employees.show', $employee))
            ->assertOk()
            ->assertSee('aria-label="Delete Alice Smith"', false);
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

    public function test_the_profile_page_links_the_phone_number_like_the_email(): void
    {
        $employee = Employee::factory()->create(['phone' => '555-0100']);

        $this->get(route('employees.show', $employee))
            ->assertOk()
            ->assertSee('href="tel:555-0100"', false);
    }

    public function test_a_missing_phone_number_falls_back_to_plain_text(): void
    {
        $employee = Employee::factory()->create(['phone' => null]);

        $this->get(route('employees.show', $employee))
            ->assertOk()
            ->assertSee('Not provided')
            ->assertDontSee('href="tel:', false);
    }

    public function test_edit_form_includes_a_custom_position_not_on_the_standard_list(): void
    {
        // position has no Rule::in constraining it to Employee::POSITIONS,
        // so an employee can genuinely have a role outside that fixed list.
        $employee = Employee::factory()->create(['position' => 'Regional Director']);

        $this->get(route('employees.edit', $employee))
            ->assertOk()
            ->assertSee('Regional Director');
    }

    public function test_edit_form_does_not_duplicate_a_standard_position(): void
    {
        $employee = Employee::factory()->create(['position' => 'Cashier']);

        $response = $this->get(route('employees.edit', $employee))->assertOk();

        $this->assertSame(1, substr_count($response->getContent(), '>Cashier<'));
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
