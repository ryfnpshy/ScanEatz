<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'session_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get cart items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate cart subtotal.
     */
    public function getSubtotalAttribute(): int
    {
        return $this->items->sum(function ($item) {
            return ($item->unit_price + $item->addons_price) * $item->quantity;
        });
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if cart is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Clear all items from cart.
     */
    public function clear(): void
    {
        $this->items()->delete();
    }

    /**
     * Scope to get active carts (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
            ->orWhereNull('expires_at');
    }
}
