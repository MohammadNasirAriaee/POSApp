<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Whitespace is stripped by the framework's TrimStrings middleware; this
     * pins that the listing keeps relying on it.
     */
    public function test_it_trims_surrounding_whitespace_from_the_search_term(): void
    {
        Employee::factory()->create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        Employee::factory()->create(['first_name' => 'Grace', 'last_name' => 'Hopper']);

        $employees = $this->get(route('employees.index', ['search' => '  Ada  ']))
            ->assertOk()
            ->viewData('employees');

        $this->assertCount(1, $employees);
        $this->assertSame('Ada', $employees->first()->first_name);
    }

    public function test_a_literal_underscore_in_the_search_term_does_not_match_as_a_wildcard(): void
    {
        Employee::factory()->create(['first_name' => 'Ann_Marie']);
        Employee::factory()->create(['first_name' => 'AnnXMarie']);

        $employees = $this->get(route('employees.index', ['search' => 'Ann_Marie']))
            ->assertOk()
            ->viewData('employees');

        $this->assertCount(1, $employees);
        $this->assertSame('Ann_Marie', $employees->first()->first_name);
    }

    public function test_a_blank_search_returns_everyone(): void
    {
        Employee::factory()->count(3)->create();

        $employees = $this->get(route('employees.index', ['search' => '   ']))
            ->assertOk()
            ->viewData('employees');

        $this->assertCount(3, $employees);
    }
}
