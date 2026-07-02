<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'processing', 'delivered'];

    protected $fillable = [
        'customer_name',
        'contact_number',
        'delivery_address',
        'status',
        'tracking_number',
    ];

    /**
     * Scope orders by a supported status.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! in_array($status, self::STATUSES, true)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
