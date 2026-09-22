<?php

namespace App\Models\Concerns;

trait EscapesLikeTerm
{
    /**
     * Escape LIKE wildcards (% and _) in a user-supplied search term so they are
     * matched literally instead of acting as SQL wildcards, e.g. searching for a
     * SKU containing an underscore should not also match unrelated rows that
     * happen to have any character in that position.
     */
    protected static function escapeLikeTerm(string $term): string
    {
        return addcslashes($term, '\\%_');
    }
}
