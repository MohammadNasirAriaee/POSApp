<?php

namespace App\Models;

use App\Models\Concerns\EscapesLikeTerm;
use App\Models\Concerns\HasFullName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use EscapesLikeTerm, HasFactory, HasFullName;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ON_LEAVE = 'on_leave';

    // Suggested roles for the create/edit form. position itself is free text
    // (no Rule::in), so an employee can hold a title outside this list.
    public const POSITIONS = [
        'Store Manager',
        'Assistant Manager',
        'Shift Supervisor',
        'Cashier',
        'Inventory Specialist',
        'Sales Associate',
        'Customer Support',
    ];

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

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return array_keys(self::statusLabels());
    }

    /**
     * Human readable name for each status, keyed by the stored value.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ON_LEAVE => 'On Leave',
        ];
    }

    /**
     * Human readable name for this employee's status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Get employee initials for avatar display.
     */
    public function getInitialsAttribute(): string
    {
        $first = mb_substr($this->first_name ?? '', 0, 1);
        $last = mb_substr($this->last_name ?? '', 0, 1);

        return strtoupper("{$first}{$last}");
    }

    /**
     * Get formatted salary string.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return '$'.number_format((float) ($this->salary ?? 0), 2);
    }

    /**
     * Get human readable tenure duration.
     */
    public function getTenureAttribute(): string
    {
        if (! $this->hire_date) {
            return 'Not specified';
        }

        $diff = $this->hire_date->diff(now());

        if ($this->hire_date->isFuture()) {
            return 'Starts '.$this->hire_date->format('M d, Y');
        }

        if ($diff->y > 0) {
            return $diff->y.' yr'.($diff->y > 1 ? 's' : '').($diff->m > 0 ? ' '.$diff->m.' mo'.($diff->m > 1 ? 's' : '') : '');
        }

        if ($diff->m > 0) {
            return $diff->m.' month'.($diff->m > 1 ? 's' : '');
        }

        if ($diff->d > 0) {
            return $diff->d.' day'.($diff->d > 1 ? 's' : '');
        }

        return 'Joined today';
    }

    /**
     * Get CSS badge classes based on status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::STATUS_ON_LEAVE => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::STATUS_INACTIVE => 'bg-slate-100 text-slate-600 ring-slate-500/20',
            default => 'bg-slate-50 text-slate-600 ring-slate-500/10',
        };
    }

    /**
     * Scope query to search by term. Matches name, email, phone, or position -
     * the search box's own placeholder lists all four.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = static::escapeLikeTerm($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->whereRaw("first_name LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("last_name LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("email LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("phone LIKE ? ESCAPE '\\'", ["%{$term}%"])
                ->orWhereRaw("position LIKE ? ESCAPE '\\'", ["%{$term}%"]);
        });
    }

    /**
     * Scope query by position.
     */
    public function scopeFilterByPosition(Builder $query, ?string $position): Builder
    {
        if (blank($position)) {
            return $query;
        }

        return $query->where('position', $position);
    }

    /**
     * Scope query by status.
     */
    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
