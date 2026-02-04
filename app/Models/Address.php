<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'full_address',
        'district',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate distance to a given point using Haversine formula.
     *
     * @param float $lat Target latitude
     * @param float $lng Target longitude
     * @return float Distance in kilometers
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371; // Earth radius in km

        $latFrom = deg2rad($this->latitude);
        $lngFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return $earthRadius * $angle;
    }

    /**
     * Scope to get default address.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get formatted address for display.
     */
    public function getFormattedAddressAttribute(): string
    {
        return "{$this->full_address}, {$this->district}, {$this->city}, {$this->province} {$this->postal_code}";
    }
}
