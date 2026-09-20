<?php

namespace App\Http\Requests\Concerns;

/**
 * Uppercases and trims the SKU field before validation runs, matching this
 * app's own convention (ProductSeeder generates SKUs like "SKU00001"). Without
 * this, "sku-100" and "SKU-100" pass the "unique" rule as different values and
 * end up as two product records tracking separate stock counts for what is
 * meant to be one physical item.
 */
trait NormalizesSku
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('sku')) {
            $this->merge(['sku' => strtoupper(trim((string) $this->input('sku')))]);
        }
    }
}
