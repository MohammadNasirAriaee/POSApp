<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    // Created but not yet paid; does not affect stock.
    public const STATUS_PENDING = 'pending';

    // Paid and stocked out; the only status that returns stock on cancel.
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'customer_id',
        'employee_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'tendered',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'tendered' => 'decimal:2',
    ];

    /**
     * The receipt reads `change`; it is derived rather than stored so it can
     * never disagree with the tendered amount and the total.
     */
    protected $appends = ['change'];

    public function getChangeAttribute(): string
    {
        if ($this->tendered === null) {
            return '0.00';
        }

        return number_format(max(0, (float) $this->tendered - (float) $this->total), 2, '.', '');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }
}
