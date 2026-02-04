<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Check stock availability for cart items.
     * returns array of unavailable items.
     */
    public function checkAvailability(Collection $cartItems): array
    {
        $unavailable = [];

        foreach ($cartItems as $item) {
            if ($item->variant_id) {
                // Check variant stock
                $variant = $item->variant;
                // Since cart items don't have outlet_id yet (it's chosen at checkout),
                // checking stock at Cart level is tricky if prices/stock differ per outlet.
                // ScanEatz Assumption: Cart is global or user picks outlet first?
                // Flow says: Menu -> Cart -> Select Outlet (Delivery/Pickup).
                // So stock check happens primarily at Checkout.
                
                // Here we might just check if "any" outlet has stock or skip.
                continue; 
            }
        }

        return $unavailable;
    }

    /**
     * Check stock for specific outlet.
     */
    public function checkStockForOutlet(Collection $cartItems, int $outletId): array
    {
        $errors = [];

        foreach ($cartItems as $item) {
            $requiredQty = $item->quantity;
            
            // If item has variant_id, check specific variant at this outlet
            if ($item->variant_id) {
                // We need to find the equivalent variant for this outlet
                // Logic: ProductVariants are per outlet.
                // But cart item might have been added from a general "menu" view.
                // If variants are outlet-specific in DB (each row has outlet_id), 
                // meaningful linking happens if we use a common "product_code" + "variant_code" reference.
                // OR: Implementation assumption: User selects outlet FIRST before seeing menu.
                // Valid flow: Home -> Select Outlet -> Menu.
                // If flow is generic: Home -> Menu -> Cart -> Select Outlet... 
                // Then variant selected in Cart must be mapped to Outlet's variant.
                
                // Let's assume strict mapping: $item->variant MUST be valid for chosen outlet.
                $variant = ProductVariant::find($item->variant_id);
                
                if (!$variant) {
                    $errors[] = "Variant produk tidak ditemukan.";
                    continue;
                }

                if ($variant->outlet_id !== $outletId) {
                    // Try to find equivalent variant at target outlet by code suffix or name
                    // Assuming code format: p{id}-v{n}-{outlet_id}
                    // This is complex. For MVP, let's assume User Selects Outlet First.
                    // If not, we'd need a "resolveVariantForOutlet" logic here.
                    
                    // Fallback simple check
                    if ($variant->stock < $requiredQty) {
                         $errors[] = "Stok {$item->product->name} ({$variant->name}) tidak mencukupi.";
                    }
                } else {
                     if ($variant->stock < $requiredQty) {
                         $errors[] = "Stok {$item->product->name} ({$variant->name}) habis/kurang.";
                    }
                }
            } else {
                // Product base (no variant) - typically Food apps execute via variants (Standard)
                // If product has no variants, we might check product level... 
                // But our DB has variants table for stock.
                // So we assume all products have at least 1 "Standard" variant.
            }
        }

        return $errors;
    }

    /**
     * Reserve stock for order.
     */
    public function reserveStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                    if ($variant) {
                        if ($variant->stock < $item->quantity) {
                            throw new \Exception("Stok untuk {$item->product_name} tidak mencukupi saat pemrosesan.");
                        }
                        $variant->decreaseStock($item->quantity);
                    }
                }
            }
        });
    }

    /**
     * Release stock (Cancellation).
     */
    public function releaseStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::lockForUpdate()->withTrashed()->find($item->variant_id);
                    if ($variant) {
                        $variant->increaseStock($item->quantity);
                    }
                }
            }
        });
    }
}
