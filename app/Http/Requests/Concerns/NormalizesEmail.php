<?php

namespace App\Http\Requests\Concerns;

/**
 * Lowercases the email field before validation runs, so "unique" rules catch
 * duplicates that differ only in case (SQLite and some MySQL collations
 * compare strings case-sensitively, which would otherwise let
 * "Ada@Example.com" and "ada@example.com" both be saved as distinct records).
 */
trait NormalizesEmail
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('email')) {
            $this->merge(['email' => strtolower((string) $this->input('email'))]);
        }
    }
}
