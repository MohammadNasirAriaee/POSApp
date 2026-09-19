<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Employee;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HasFullNameTest extends TestCase
{
    public static function modelProvider(): array
    {
        return [
            'customer' => [Customer::class],
            'employee' => [Employee::class],
        ];
    }

    #[DataProvider('modelProvider')]
    public function test_it_builds_and_appends_the_full_name(string $model): void
    {
        $record = new $model(['first_name' => 'Ada', 'last_name' => 'Lovelace']);

        $this->assertSame('Ada Lovelace', $record->name);
        $this->assertArrayHasKey('name', $record->toArray());
        $this->assertSame('Ada Lovelace', $record->toArray()['name']);
    }

    #[DataProvider('modelProvider')]
    public function test_a_missing_last_name_leaves_no_trailing_space(string $model): void
    {
        $record = new $model(['first_name' => 'Cher']);

        $this->assertSame('Cher', $record->name);
    }
}
