<?php

namespace App\Models;

use App\Models\Concerns\EscapesLikeTerm;
use App\Models\Concerns\HasFullName;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use EscapesLikeTerm;

    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    use HasFullName;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Matches name, phone, or email - the fields a cashier is most likely to
    // have on hand when looking a customer up.
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = static::escapeLikeTerm($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->whereRaw("first_name LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("last_name LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("phone LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("email LIKE ? ESCAPE '\\'", ["%{$term}%"]);
        });
    }
}
