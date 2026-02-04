<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',
        'preferred_locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get user's addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get user's default address.
     */
    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    /**
     * Get user's shopping carts.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get user's active cart.
     */
    public function activeCart()
    {
        return $this->carts()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Get user's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get coupons used by user.
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user')
            ->withPivot('usage_count', 'last_used_at')
            ->withTimestamps();
    }

    /**
     * Check if user can use a specific coupon.
     */
    public function canUseCoupon(Coupon $coupon): bool
    {
        if (!$coupon->is_active) {
            return false;
        }

        $usage = $this->coupons()
            ->where('coupons.id', $coupon->id)
            ->first();

        if (!$usage) {
            return true; // First time using
        }

        return $usage->pivot->usage_count < $coupon->usage_per_user;
    }

    /**
     * Check if user is authenticated via Google OAuth.
     */
    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }
}
