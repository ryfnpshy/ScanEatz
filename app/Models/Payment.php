<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'PENDING';
    const STATUS_PAID = 'PAID';
    const STATUS_FAILED = 'FAILED';
    const STATUS_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'order_id',
        'payment_code',
        'method',
        'amount',
        'status',
        'external_id',
        'payment_url',
        'metadata',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'metadata' => 'array',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is expired.
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Scope to get pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
