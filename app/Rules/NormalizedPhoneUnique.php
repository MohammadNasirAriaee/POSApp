<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * Formatting-insensitive counterpart to Laravel's built-in "unique" rule, for
 * phone numbers.
 *
 * The built-in rule always compares with "=", so "555-0100" and "5550100"
 * pass as distinct values even though they are the same number - a cashier
 * who types a returning customer's number with different punctuation than it
 * was originally entered ends up creating a duplicate customer record
 * instead of matching the existing one. Like phone numbers themselves, the
 * stored value keeps whatever punctuation the user entered (for display); only
 * the comparison strips it.
 */
class NormalizedPhoneUnique implements ValidationRule
{
    private const SEPARATORS = [' ', '-', '(', ')', '+', '.'];

    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly int|string|null $ignoreId = null,
        private readonly string $ignoreColumn = 'id',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = $this->strip((string) $value);

        if ($normalized === '') {
            return;
        }

        $query = DB::table($this->table)
            ->whereRaw($this->strippedColumnSql().' = ?', [$normalized]);

        if ($this->ignoreId !== null) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('The :attribute has already been taken.');
        }
    }

    private function strip(string $value): string
    {
        return str_replace(self::SEPARATORS, '', $value);
    }

    private function strippedColumnSql(): string
    {
        $sql = $this->column;

        foreach (self::SEPARATORS as $separator) {
            $sql = "REPLACE({$sql}, '{$separator}', '')";
        }

        return $sql;
    }
}
