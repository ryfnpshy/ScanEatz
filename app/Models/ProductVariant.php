<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'product_id',
        'outlet_id',
        'name',
        'variant_type',
        'price_adjustment',
        'stock',
        'min_stock_alert',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price_adjustment' => 'integer',
            'stock' => 'integer',
            'min_stock_alert' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the outlet that stocks this variant.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get final price (base + adjustment).
     */
    public function getFinalPriceAttribute(): int
    {
        return $this->product->base_price + $this->price_adjustment;
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock_alert;
    }

    /**
     * Check if variant is in stock.
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->is_available && $this->stock >= $quantity;
    }

    /**
     * Decrease stock.
     */
    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
        
        if ($this->stock <= 0) {
            $this->update(['is_available' => false]);
        }
    }

    /**
     * Increase stock (for cancellations/refunds).
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
        
        if ($this->stock > 0 && !$this->is_available) {
            $this->update(['is_available' => true]);
        }
    }

    /**
     * Scope to get available variants.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }
}
