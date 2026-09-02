<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
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
}
