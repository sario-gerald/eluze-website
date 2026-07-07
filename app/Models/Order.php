<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'processing', 'delivered'];

    protected $appends = [
        'order_reference',
    ];

    protected $fillable = [
        'user_id',
        'customer_name',
        'contact_number',
        'delivery_address',
        'subtotal',
        'shipping_fee',
        'total',
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

    /**
     * Customer account that placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Products purchased in this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Public-facing reference that does not reveal the database ID sequence.
     */
    public function getOrderReferenceAttribute(): string
    {
        $datePart = $this->created_at?->format('Ymd') ?? now()->format('Ymd');
        $hash = strtoupper(substr(hash_hmac(
            'sha256',
            $this->getKey().'|'.$this->customer_name.'|'.$datePart,
            config('app.key')
        ), 0, 6));

        return "ELZ-{$datePart}-{$hash}";
    }
}
