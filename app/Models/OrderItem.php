<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * name and price are snapshotted from the product at sale time, not read
 * live from it - a later price change or rename must never rewrite a
 * historical receipt.
 */
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

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

    /**
     * The order this line item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The live product this line item snapshot was taken from, if it still exists.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
