<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAssignment extends Model
{
    use HasFactory;

    const STATUS_ASSIGNED = 'ASSIGNED';
    const STATUS_PICKED_UP = 'PICKED_UP';
    const STATUS_DELIVERED = 'DELIVERED';

    protected $fillable = [
        'order_id',
        'driver_name',
        'driver_phone',
        'vehicle_type',
        'vehicle_plate',
        'status',
        'picked_up_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
