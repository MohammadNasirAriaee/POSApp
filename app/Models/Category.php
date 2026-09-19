<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name', // name of the category
        'slug', // slug for the category
        'description', // description of the category
        'is_active', // whether the category is active or not
    ];

    protected $casts = [
        'is_active' => 'boolean', // cast is_active to boolean
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class); // a category can have many products
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Build a slug that is unique across categories. Distinct names can reduce
     * to the same slug ("Foo Bar" and "Foo-Bar"), which would otherwise hit the
     * unique index, so collisions get a numeric suffix.
     */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
