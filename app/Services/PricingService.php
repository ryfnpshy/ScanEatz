<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Outlet;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Support\Collection;

class PricingService
{
    protected DeliveryService $deliveryService;

    public function __construct(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Calculate delivery fee.
     */
    public function calculateDeliveryFee(Outlet $outlet, Address $address): int
    {
        if (!$outlet->canDeliverTo($address)) {
            throw new \Exception('Alamat di luar jangkauan pengiriman outlet ini.');
        }
        
        $distance = $address->distanceTo($outlet->latitude, $outlet->longitude);
        
        // Pricing Strategy:
        // Base fee: 5.000 (0-2km)
        // Next km: 2.000/km (ceil)
        
        $baseFee = 5000;
        
        if ($distance <= 2) {
            return $baseFee;
        }
        
        $extraKm = ceil($distance - 2);
        return $baseFee + ($extraKm * 2000);
    }

    /**
     * Calculate discount from coupon.
     */
    public function calculateDiscount(?Coupon $coupon, int $subtotal): int
    {
        if (!$coupon) {
            return 0;
        }
        
        return $coupon->calculateDiscount($subtotal);
    }

    /**
     * Calculate Order Totals Breakdown.
     */
    public function calculateOrderTotals(
        Collection $cartItems,
        Outlet $outlet,
        ?Address $address = null,
        ?Coupon $coupon = null,
        string $fulfillmentType = 'delivery'
    ): array
    {
        // 1. Calculate items subtotal
        $subtotal = $cartItems->sum(fn ($item) => $item->line_total);
        $addonsTotal = $cartItems->sum(fn ($item) => $item->addons_price * $item->quantity);

        // 2. Tax (e.g., 10% PB1)
        // Note: Often prices are inclusive, but let's assume exclusive for clarity or configurable
        // Assumption: Prices in DB are exclusive of tax
        $taxRate = 0.10;
        $tax = (int) round($subtotal * $taxRate);

        // 3. Delivery Fee
        $deliveryFee = 0;
        if ($fulfillmentType === 'delivery' && $address) {
            $deliveryFee = $this->calculateDeliveryFee($outlet, $address);
        }

        // 4. Discount
        $discount = $this->calculateDiscount($coupon, $subtotal);

        // 5. Total
        $total = $subtotal + $tax + $deliveryFee - $discount;
        
        // Rounding logic (round to nearest hundred)
        $total = round($total / 100) * 100;

        return [
            'subtotal' => $subtotal,
            'addons_total' => $addonsTotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'discount' => $discount,
            'total' => max(0, $total),
            'currency' => 'IDR',
        ];
    }
}
