<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function products() // define the relationship between Category and Product models
    {
        return $this->hasMany(Product::class);
    }
}
