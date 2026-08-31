<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    protected $fillable = [ // fillable attributes for mass assignment
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'cost',
        'stock_quantity',
        'image',
        'status',
    ];

    protected $casts = [ // cast attributes to specific data types
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function category() // define the relationship between Product and Category models
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems() // define the relationship between Product and OrderItem models
    {
        return $this->hasMany(OrderItem::class);
    }
}
