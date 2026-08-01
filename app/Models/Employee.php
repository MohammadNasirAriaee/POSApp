<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'position',
        'salary',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
