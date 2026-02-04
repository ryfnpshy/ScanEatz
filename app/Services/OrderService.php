<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    protected CartService $cartService;
    protected PricingService $pricingService;
    protected StockService $stockService;

    public function __construct(
        CartService $cartService,
        PricingService $pricingService,
        StockService $stockService
    ) {
        $this->cartService = $cartService;
        $this->pricingService = $pricingService;
        $this->stockService = $stockService;
    }

    /**
     * Create Order from Checkout Data.
     */
    public function checkout(User $user, array $data): Order
    {
        // 1. Validate Data
        $cartId = $data['cart_id'];
        $outletId = $data['outlet_id'];
        $addressId = $data['address_id'] ?? null;
        $fulfillment = $data['fulfillment'] ?? 'delivery'; // delivery/pickup
        $paymentMethod = $data['payment_method'];
        $couponCode = $data['coupon'] ?? null;
        $scheduledAt = $data['scheduled_at'] ?? null;

        $cart = Cart::with('items.variant', 'items.product')->findOrFail($cartId);
        
        if ($cart->isEmpty()) {
            throw new \Exception('Keranjang belanja kosong.');
        }

        $outlet = Outlet::findOrFail($outletId);
        
        // 2. Validate Address & Radius (if delivery)
        $address = null;
        if ($fulfillment === 'delivery') {
            if (!$addressId) {
                throw new \Exception('Alamat wajib diisi untuk delivery.');
            }
            $address = Address::where('user_id', $user->id)->findOrFail($addressId);
            
            if (!$outlet->canDeliverTo($address)) {
                throw new \Exception('Alamat di luar jangkauan pengiriman.');
            }
        }

        // 3. Validate Stock
        $stockErrors = $this->stockService->checkStockForOutlet($cart->items, $outletId);
        if (!empty($stockErrors)) {
            throw new \Exception(implode(', ', $stockErrors));
        }

        // 4. Validate Coupon
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            // Basic validation logic here (can be moved to CouponService)
            if (!$coupon || !$coupon->isValid($user)) {
                // throw new \Exception('Kupon tidak valid.'); 
                // Or just ignore invalid coupon
                $coupon = null;
            }
        }

        // 5. Calculate Totals
        $totals = $this->pricingService->calculateOrderTotals(
            $cart->items,
            $outlet,
            $address,
            $coupon,
            $fulfillment
        );

        // 6. DB Transaction: Create Order
        return DB::transaction(function () use ($user, $cart, $outlet, $address, $totals, $fulfillment, $scheduledAt, $paymentMethod, $coupon) {
            
            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'outlet_id' => $outlet->id,
                'address_id' => $address?->id,
                'fulfillment_type' => $fulfillment,
                'scheduled_at' => $scheduledAt,
                'status' => Order::STATUS_PENDING, // Start as Pending
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax'],
                'delivery_fee' => $totals['delivery_fee'],
                'discount_amount' => $totals['discount'],
                'total_amount' => $totals['total'],
                'eta_minutes' => $address ? $outlet->calculateETA($address) : $outlet->base_eta_minutes, // Initial ETA
            ]);

            // Create Order Items (Snapshot)
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'addons_snapshot' => $item->addons, // JSON
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'addons_price' => $item->addons_price,
                    'line_total' => $item->line_total,
                    'notes' => $item->notes,
                ]);
            }

            // Create Payment Record
            $order->payment()->create([
                'payment_code' => 'PAY-' . Str::upper(Str::random(10)),
                'method' => $paymentMethod,
                'amount' => $totals['total'],
                'status' => 'PENDING',
                'expired_at' => now()->addMinutes(15), // 15 min payment window
            ]);

            // Track Coupon Usage
            if ($coupon) {
                $existing = $user->coupons()->where('coupons.id', $coupon->id)->first();
                if ($existing) {
                    $user->coupons()->updateExistingPivot($coupon->id, [
                        'usage_count' => $existing->pivot->usage_count + 1,
                        'last_used_at' => now(),
                    ]);
                } else {
                    $user->coupons()->attach($coupon->id, [
                        'usage_count' => 1,
                        'last_used_at' => now(),
                    ]);
                }
                $coupon->increment('used_count');
            }

            // Clear Cart
            $cart->items()->delete();
            $cart->delete(); // Or just clear items

            return $order;
        });
    }

    /**
     * Process Payment Success (Webhook/Callback).
     */
    public function confirmPayment(string $paymentCode): void
    {
        $order = Order::whereHas('payment', fn($q) => $q->where('payment_code', $paymentCode))->firstOrFail();
        
        if ($order->status !== Order::STATUS_PENDING) {
            return; // Already processed
        }

        DB::transaction(function () use ($order) {
            // Update Payment
            $order->payment()->update(['status' => 'PAID', 'paid_at' => now()]);

            // Update Order Status
            $order->transitionTo(Order::STATUS_CONFIRMED);

            // Reserve Stock
            $this->stockService->reserveStock($order);

            // TODO: Dispatch Event/Notification
            // event(new OrderConfirmed($order));
        });
    }

    /**
     * Cancel Order.
     */
    public function cancelOrder(Order $order, string $reason, ?int $userId = null): void
    {
        if (!$order->isCancellable()) {
            throw new \Exception('Pesanan tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order, $reason, $userId) {
            $previousStatus = $order->status;
            
            $order->cancel($reason, $userId);

            // If order was already confirmed (stock reserved), release it
            if ($previousStatus === Order::STATUS_CONFIRMED) {
                $this->stockService->releaseStock($order);
                
                // If paid, trigger refund logic
                if ($order->payment && $order->payment->isPaid()) {
                    $order->payment()->update(['status' => 'REFUNDED']);
                    // Trigger actual refund gateway API...
                }
            } else {
                 // Pending order -> Failed payment
                 if ($order->payment) {
                     $order->payment()->update(['status' => 'FAILED']);
                 }
            }
        });
    }
}
