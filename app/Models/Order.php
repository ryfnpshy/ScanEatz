<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    // Status constants for state machine
    const STATUS_PENDING = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_COOKING = 'COOKING';
    const STATUS_READY = 'READY';
    const STATUS_ON_DELIVERY = 'ON_DELIVERY';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'order_code',
        'user_id',
        'outlet_id',
        'address_id',
        'fulfillment_type',
        'scheduled_at',
        'status',
        'status_timeline',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'eta_minutes',
        'confirmed_at',
        'cooking_started_at',
        'ready_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'customer_notes',
        'merchant_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status_timeline' => 'array',
            'subtotal' => 'integer',
            'tax_amount' => 'integer',
            'delivery_fee' => 'integer',
            'discount_amount' => 'integer',
            'total_amount' => 'integer',
            'eta_minutes' => 'integer',
            'confirmed_at' => 'datetime',
            'cooking_started_at' => 'datetime',
            'ready_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Boot method to generate order code.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = static::generateOrderCode();
            }

            if (empty($order->status_timeline)) {
                $order->status_timeline = [
                    ['status' => $order->status, 'timestamp' => now()->toISOString()]
                ];
            }
        });
    }

    /**
     * Generate unique order code.
     */
    public static function generateOrderCode(): string
    {
        $year = now()->format('Y');
        $random = strtoupper(Str::random(6));
        
        return "ORD-{$year}-{$random}";
    }

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the outlet fulfilling the order.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the delivery address.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get payment for this order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get delivery assignment.
     */
    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    /**
     * Transition order to new status with validation.
     */
    public function transitionTo(string $newStatus): bool
    {
        $validTransitions = [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_COOKING, self::STATUS_CANCELLED],
            self::STATUS_COOKING => [self::STATUS_READY],
            self::STATUS_READY => [self::STATUS_ON_DELIVERY, self::STATUS_COMPLETED],
            self::STATUS_ON_DELIVERY => [self::STATUS_COMPLETED],
        ];

        $currentStatus = $this->status;

        if (!isset($validTransitions[$currentStatus]) || 
            !in_array($newStatus, $validTransitions[$currentStatus])) {
            return false; // Invalid transition
        }

        // Update status
        $this->status = $newStatus;

        // Update timeline
        $timeline = $this->status_timeline ?? [];
        $timeline[] = [
            'status' => $newStatus,
            'timestamp' => now()->toISOString()
        ];
        $this->status_timeline = $timeline;

        // Update timestamp fields
        match ($newStatus) {
            self::STATUS_CONFIRMED => $this->confirmed_at = now(),
            self::STATUS_COOKING => $this->cooking_started_at = now(),
            self::STATUS_READY => $this->ready_at = now(),
            self::STATUS_COMPLETED => $this->completed_at = now(),
            self::STATUS_CANCELLED => $this->cancelled_at = now(),
            default => null,
        };

        return $this->save();
    }

    /**
     * Cancel the order.
     */
    public function cancel(string $reason, ?int $cancelledBy = null): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false; // Can only cancel if PENDING or CONFIRMED
        }

        $this->cancellation_reason = $reason;
        $this->cancelled_by = $cancelledBy;

        return $this->transitionTo(self::STATUS_CANCELLED);
    }

    /**
     * Check if order can be cancelled.
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /** 
     * Check if order is in final state.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if order is active.
     */
    public function isActive(): bool
    {
        return !$this->isCompleted();
    }

    /**
     * Scope to get orders by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get active orders.
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope to get orders for a specific outlet.
     */
    public function scopeForOutlet($query, int $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get status display in Indonesian.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_COOKING => 'Sedang Dimasak',
            self::STATUS_READY => 'Siap',
            self::STATUS_ON_DELIVERY => 'Dalam Pengiriman',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Unknown',
        };
    }
}
