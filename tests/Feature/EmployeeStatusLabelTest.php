<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeStatusLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_toggle_flash_names_the_new_status(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        // active -> on_leave
        $this->patch(route('employees.toggle-status', $employee))
            ->assertSessionHas('success', 'Status for Ada Lovelace changed to On Leave.');

        $this->assertSame(Employee::STATUS_ON_LEAVE, $employee->fresh()->status);
    }

    public function test_an_explicit_status_is_honoured_and_labelled(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $this->patch(route('employees.toggle-status', $employee), [
            'new_status' => Employee::STATUS_INACTIVE,
        ])->assertSessionHas('success', 'Status for Grace Hopper changed to Inactive.');

        $this->assertSame(Employee::STATUS_INACTIVE, $employee->fresh()->status);
    }

    public function test_the_listing_and_detail_pages_render_the_proper_label(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'status' => Employee::STATUS_ON_LEAVE,
        ]);

        // "On Leave", not the "On leave" the old inline formatting produced.
        $this->get(route('employees.index'))->assertOk()->assertSee('On Leave');
        $this->get(route('employees.show', $employee))->assertOk()->assertSee('On Leave');
    }

    public function test_statuses_stay_in_sync_with_the_label_map(): void
    {
        $this->assertSame(Employee::statuses(), array_keys(Employee::statusLabels()));
    }
}
