<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [ // cast the price and subtotal to decimal with 2 decimal places, and quantity to integer
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order() // define the relationship between OrderItem and Order models
    {
        return $this->belongsTo(Order::class);
    }

    public function product() // define the relationship between OrderItem and Product models
    {
        return $this->belongsTo(Product::class);
    }
}
