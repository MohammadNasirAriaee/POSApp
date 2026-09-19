<?php

namespace App\Models\Concerns;

/**
 * Exposes a computed `name` built from first and last name.
 *
 * Inertia pages and Blade views read `name` on both customers and employees,
 * so the attribute is appended to JSON payloads. A missing last name must not
 * leave a trailing space, hence the trim.
 */
trait HasFullName
{
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function initializeHasFullName(): void
    {
        $this->appends = array_values(array_unique([...$this->appends, 'name']));
    }
}
