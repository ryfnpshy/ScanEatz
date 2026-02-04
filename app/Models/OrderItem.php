<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'addons_snapshot',
        'quantity',
        'unit_price',
        'addons_price',
        'line_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'addons_snapshot' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'addons_price' => 'integer',
            'line_total' => 'integer',
        ];
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the original product (might be deleted/changed).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the original variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class)->withTrashed(); // Variants might be soft deleted or we just want reference
    }

    /**
     * Get formatted line total.
     */
    public function getFormattedLineTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->line_total, 0, ',', '.');
    }

    /**
     * Get formatted individual price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }
}
