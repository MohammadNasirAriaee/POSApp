<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * Case-insensitive counterpart to Laravel's built-in "unique" rule.
 *
 * The built-in rule always compares with "=", so "Beverages" and "BEVERAGES"
 * pass as distinct values even though a category list showing both would
 * confuse anyone picking one for a product. Unlike email or SKU, a category
 * name's casing is meant to be preserved for display, so the fix here is a
 * case-insensitive comparison rather than normalizing the stored value.
 */
class CaseInsensitiveUnique implements ValidationRule
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly int|string|null $ignoreId = null,
        private readonly string $ignoreColumn = 'id',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)
            ->whereRaw('LOWER('.$this->column.') = ?', [mb_strtolower((string) $value)]);

        if ($this->ignoreId !== null) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('The :attribute has already been taken.');
        }
    }
}
