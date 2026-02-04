<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\Address;
use Carbon\Carbon;

class DeliveryService
{
    /**
     * Check if address is within delivery radius.
     */
    public function isWithinRadius(Outlet $outlet, Address $address): bool
    {
        return $outlet->canDeliverTo($address);
    }

    /**
     * Get outlets available for an address.
     */
    public function getAvailableOutletsForAddress(Address $address)
    {
        // Get all active outlets
        $outlets = Outlet::active()->get();
        
        // Filter by radius
        return $outlets->filter(function ($outlet) use ($address) {
            return $this->isWithinRadius($outlet, $address);
        })->values();
    }

    /**
     * Calculate ETA (Estimated Time of Arrival).
     */
    public function calculateETA(Outlet $outlet, Address $address): array
    {
        $minutes = $outlet->calculateETA($address);
        $arrivalTime = now()->addMinutes($minutes);

        return [
            'minutes' => $minutes,
            'time' => $arrivalTime,
            'formatted_time' => $arrivalTime->format('H:i'),
        ];
    }

    /**
     * Get available delivery slots (for scheduled orders).
     */
    public function getDeliverySlots(Outlet $outlet): array
    {
        // Simple implementation: Every 30 mins for next 2 days
        // In real app: check driver availability / manufacturing capacity
        
        $slots = [];
        $start = now()->addMinutes(45)->roundMinute(30); // Earliest slot
        $end = now()->addDays(2)->endOfDay();
        
        $current = $start->copy();
        
        while ($current <= $end) {
            if ($outlet->isOpen($current)) {
                $slots[] = [
                    'value' => $current->toIso8601String(),
                    'label' => $current->isoFormat('dddd, D MMM - HH:mm'),
                ];
            }
            $current->addMinutes(30);
        }
        
        return $slots;
    }
}
