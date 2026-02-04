<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_subtotal',
        'max_discount',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_subtotal' => 'integer',
            'max_discount' => 'integer',
            'usage_limit' => 'integer',
            'usage_per_user' => 'integer',
            'used_count' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Users who have used this coupon.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot('usage_count', 'last_used_at')
            ->withTimestamps();
    }

    /**
     * Check if coupon is valid for use.
     */
    public function isValid(?User $user = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($user) {
            return $user->canUseCoupon($this);
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(int $subtotal): int
    {
        if ($subtotal < $this->min_subtotal) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $subtotal); // Cannot exceed subtotal
        }

        // Percentage
        $discount = $subtotal * ($this->value / 100);

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return (int) $discount;
    }
}
