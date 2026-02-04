<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'district',
        'city',
        'latitude',
        'longitude',
        'delivery_radius_km',
        'phone',
        'email',
        'is_active',
        'base_eta_minutes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'delivery_radius_km' => 'decimal:2',
            'is_active' => 'boolean',
            'base_eta_minutes' => 'integer',
        ];
    }

    /**
     * Get outlet's operating hours.
     */
    public function operatingHours(): HasMany
    {
        return $this->hasMany(OperatingHour::class);
    }

    /**
     * Get outlet's blackout dates.
     */
    public function blackoutDates(): HasMany
    {
        return $this->hasMany(OutletBlackoutDate::class);
    }

    /**
     * Get outlet's product variants (stock).
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get outlet's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if outlet is currently open.
     *
     * @param Carbon|null $dateTime
     * @return bool
     */
    public function isOpen(?Carbon $dateTime = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $dateTime = $dateTime ?? now();
        
        // Check blackout dates
        if ($this->blackoutDates()->whereDate('blackout_date', $dateTime->toDateString())->exists()) {
            return false;
        }

        // Check operating hours for current day
        $dayOfWeek = $dateTime->dayOfWeek;
        $currentTime = $dateTime->format('H:i:s');

        $hours = $this->operatingHours()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_closed', false)
            ->first();

        if (!$hours) {
            return false;
        }

        return $currentTime >= $hours->open_time && $currentTime <= $hours->close_time;
    }

    /**
     * Calculate ETA for delivery to given address.
     *
     * @param Address $address
     * @param int $cookingTimeMinutes
     * @return int ETA in minutes
     */
    public function calculateETA(Address $address, int $cookingTimeMinutes = 15): int
    {
        $distance = $address->distanceTo($this->latitude, $this->longitude);
        
        // Base ETA + cooking time + delivery time (3 min per km)
        $deliveryTime = ceil($distance * 3);
        
        return $this->base_eta_minutes + $cookingTimeMinutes + $deliveryTime;
    }

    /**
     * Check if address is within delivery radius.
     */
    public function canDeliverTo(Address $address): bool
    {
        $distance = $address->distanceTo($this->latitude, $this->longitude);
        
        return $distance <= $this->delivery_radius_km;
    }

    /**
     * Scope to get active outlets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get outlets near a location.
     */
    public function scopeNear($query, float $lat, float $lng, float $radiusKm = 10)
    {
        // Using Haversine formula in SQL
        return $query->selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance_km
        ", [$lat, $lng, $lat])
        ->having('distance_km', '<=', $radiusKm)
        ->orderBy('distance_km');
    }
}
