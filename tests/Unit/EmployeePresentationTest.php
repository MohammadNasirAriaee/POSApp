<?php

namespace Tests\Unit;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The presentation accessors are pure derivations of model state, so these
 * exercise them directly without touching the database.
 */
class EmployeePresentationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-06-15 14:30:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_builds_a_full_name_and_initials(): void
    {
        $employee = new Employee(['first_name' => 'Ada', 'last_name' => 'Lovelace']);

        $this->assertSame('Ada Lovelace', $employee->name);
        $this->assertSame('AL', $employee->initials);
    }

    public function test_a_missing_last_name_does_not_leave_a_trailing_space(): void
    {
        $employee = new Employee(['first_name' => 'Cher']);

        $this->assertSame('Cher', $employee->name);
        $this->assertSame('C', $employee->initials);
    }

    public function test_it_formats_salary_including_when_unset(): void
    {
        $this->assertSame('$1,234.50', (new Employee(['salary' => 1234.5]))->formatted_salary);
        $this->assertSame('$0.00', (new Employee)->formatted_salary);
    }

    public static function tenureProvider(): array
    {
        return [
            'not set' => [null, 'Not specified'],
            'starts later' => ['2026-07-01', 'Starts Jul 01, 2026'],
            'today' => ['2026-06-15', 'Joined today'],
            'one day' => ['2026-06-14', '1 day'],
            'several days' => ['2026-06-10', '5 days'],
            'one month' => ['2026-05-15', '1 month'],
            'several months' => ['2026-04-15', '2 months'],
            'one year' => ['2025-06-15', '1 yr'],
            'years and months' => ['2024-03-15', '2 yrs 3 mos'],
            'years and one month' => ['2024-05-15', '2 yrs 1 mo'],
        ];
    }

    #[DataProvider('tenureProvider')]
    public function test_it_describes_tenure(?string $hireDate, string $expected): void
    {
        $employee = new Employee(['hire_date' => $hireDate]);

        $this->assertSame($expected, $employee->tenure);
    }

    public function test_each_status_has_its_own_badge_styling(): void
    {
        $classes = [];

        foreach (Employee::statuses() as $status) {
            $class = (new Employee(['status' => $status]))->status_badge_class;
            $this->assertNotEmpty($class);
            $classes[] = $class;
        }

        $this->assertSame($classes, array_unique($classes), 'Statuses should be visually distinct.');
    }
}
