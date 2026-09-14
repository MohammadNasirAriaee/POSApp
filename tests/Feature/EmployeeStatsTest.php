<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reports_headcount_payroll_and_average_salary(): void
    {
        Employee::factory()->create(['status' => Employee::STATUS_ACTIVE, 'salary' => 1000]);
        Employee::factory()->create(['status' => Employee::STATUS_ACTIVE, 'salary' => 3000]);
        Employee::factory()->create(['status' => Employee::STATUS_ON_LEAVE, 'salary' => 9999]);
        Employee::factory()->create(['status' => Employee::STATUS_INACTIVE, 'salary' => 8888]);

        $stats = $this->get(route('employees.index'))->assertOk()->viewData('stats');

        $this->assertSame(4, $stats['total']);
        $this->assertSame(2, $stats['active']);
        $this->assertSame(1, $stats['on_leave']);
        $this->assertSame(1, $stats['inactive']);
        $this->assertEqualsWithDelta(4000.0, $stats['monthly_payroll'], 0.001);
        $this->assertEqualsWithDelta(2000.0, $stats['avg_salary'], 0.001);
    }

    public function test_it_reports_zeroes_when_there_are_no_employees(): void
    {
        $stats = $this->get(route('employees.index'))->assertOk()->viewData('stats');

        $this->assertSame(0, $stats['total']);
        $this->assertSame(0, $stats['active']);
        $this->assertEqualsWithDelta(0.0, $stats['avg_salary'], 0.001);
    }
}
