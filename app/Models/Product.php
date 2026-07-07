<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const COLLECTIONS = [
        'women' => "Women's Collection",
        'men' => "Men's Collection",
        'unisex' => 'Unisex Collection',
    ];

    public const SIZES = [10, 30, 50, 75, 100];

    protected $fillable = [
        'name',
        'collection',
        'scent',
        'inspiration',
        'price_10ml',
        'price_30ml',
        'price_50ml',
        'price_75ml',
        'price_100ml',
        'stock',
        'low_stock_threshold',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search) {
            $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('collection', 'like', "%{$search}%")
                ->orWhere('scent', 'like', "%{$search}%");
        });
    }

    public function priceForSize(int $size): int
    {
        return (int) $this->getAttribute("price_{$size}ml");
    }

    public function getCollectionLabelAttribute(): string
    {
        return self::COLLECTIONS[$this->collection] ?? ucfirst($this->collection);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out';
        }

        if ($this->stock <= $this->low_stock_threshold) {
            return 'low';
        }

        return 'available';
    }
}
