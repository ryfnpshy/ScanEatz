<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PricingService;
use App\Models\Address;
use App\Models\Outlet;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Checkout extends Component
{
    public $cart;
    public $addresses = [];
    public $outlets = [];
    
    // Form Data
    public $fulfillmentType = 'delivery'; // delivery, pickup
    public $selectedAddressId;
    public $selectedOutletId;
    public $paymentMethod = 'COD'; // COD, BANK_TRANSFER
    public $couponCode = '';
    public $notes = '';
    
    // Totals
    public $totals = [];
    public $errorMessage = '';

    public function mount(CartService $cartService)
    {
        $this->cart = $cartService->getActiveCart();
        
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart');
        }

        $this->addresses = Auth::user()->addresses;
        
        // Auto-select default address
        if ($default = Auth::user()->defaultAddress()) {
            $this->selectedAddressId = $default->id;
        } elseif ($this->addresses->count() > 0) {
            $this->selectedAddressId = $this->addresses->first()->id;
        }

        // Load Outlets and try to select nearest
        $this->outlets = Outlet::active()->get();
        
        if ($this->selectedAddressId) {
            $address = $this->addresses->find($this->selectedAddressId);
            // Simple nearest logic
            $nearest = $this->outlets->sortBy(function($outlet) use ($address) {
                return $outlet->distanceTo($address->latitude, $address->longitude);
            })->first();
            
            if ($nearest) {
                $this->selectedOutletId = $nearest->id;
            }
        } else {
             $this->selectedOutletId = $this->outlets->first()->id ?? null;
        }

        $this->calculateTotals();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['fulfillmentType', 'selectedAddressId', 'selectedOutletId', 'couponCode'])) {
            $this->calculateTotals();
        }
    }

    public function applyCoupon()
    {
        $this->calculateTotals();
        
        if ($this->totals['discount'] > 0) {
             $this->dispatch('notify', message: 'Kupon berhasil digunakan!');
        } else {
             $this->dispatch('notify', message: 'Kupon tidak valid / syarat tidak terpenuhi.', type: 'error');
        }
    }

    public function calculateTotals()
    {
        $pricingService = app(PricingService::class);
        $outlet = Outlet::find($this->selectedOutletId);
        $address = Address::find($this->selectedAddressId);
        
        // Find Coupon
        $coupon = null;
        if (!empty($this->couponCode)) {
            $coupon = Coupon::where('code', $this->couponCode)->first();
        }

        try {
            if ($outlet) {
                $this->totals = $pricingService->calculateOrderTotals(
                    $this->cart->items,
                    $outlet,
                    $address, // Can be null if pickup
                    $coupon,
                    $this->fulfillmentType
                );
                $this->errorMessage = '';
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->totals = [
                'subtotal' => $this->cart->subtotal,
                'tax' => 0, 'delivery_fee' => 0, 'discount' => 0, 'total' => 0
            ];
        }
    }

    public function placeOrder(OrderService $orderService)
    {
        $this->validate([
            'paymentMethod' => 'required',
            'selectedOutletId' => 'required',
            'selectedAddressId' => 'required_if:fulfillmentType,delivery',
        ]);

        try {
            $order = $orderService->checkout(Auth::user(), [
                'cart_id' => $this->cart->id,
                'outlet_id' => $this->selectedOutletId,
                'address_id' => $this->selectedAddressId,
                'fulfillment' => $this->fulfillmentType,
                'payment_method' => $this->paymentMethod,
                'coupon' => $this->couponCode,
            ]);

            // Redirect to Tracking / Order Detail
             return redirect()->route('catalog'); // Placeholder, later ->route('orders.show', $order)
            // But since tracking is public via tracking code, maybe show success modal first?
            // For now, redirect catalog with success message? No, usually Order Detail.
            // I'll assume I can redirect to a tracking page later.
            // Let's redirect to home with flash message as fallback if route missing.
            
            session()->flash('success', 'Pesanan berhasil dibuat! Kode: ' . $order->order_code);
            return redirect('/'); 

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal membuat pesanan: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.checkout')->layout('layouts.app');
    }
}
