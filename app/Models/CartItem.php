<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'addons',
        'quantity',
        'notes',
        'unit_price',
        'addons_price',
    ];

    protected function casts(): array
    {
        return [
            'addons' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'addons_price' => 'integer',
        ];
    }

    /**
     * Get the cart that owns the item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Calculate line total.
     */
    public function getLineTotalAttribute(): int
    {
        return ($this->unit_price + $this->addons_price) * $this->quantity;
    }

    /**
     * Get addon details.
     */
    public function getAddonDetails()
    {
        if (empty($this->addons)) {
            return collect();
        }

        return Addon::whereIn('id', $this->addons)->get();
    }

    /**
     * Update quantity.
     */
    public function updateQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            $this->delete();
            return;
        }

        $this->update(['quantity' => $quantity]);
    }
}
