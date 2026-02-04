<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get or create active cart for current user/guest.
     */
    public function getActiveCart(): Cart
    {
        $user = Auth::user();
        $sessionId = Session::getId();

        if ($user) {
            // Find existing active cart for user
            $cart = Cart::where('user_id', $user->id)
                ->active()
                ->latest()
                ->first();

            if (!$cart) {
                // Check if there's a guest cart to merge
                $guestCart = Cart::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->active()
                    ->first();
                
                if ($guestCart) {
                    $guestCart->update(['user_id' => $user->id]);
                    return $guestCart;
                }

                // Create new cart
                $cart = Cart::create([
                    'code' => 'crt-' . Str::random(10),
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                ]);
            }
            
            return $cart;

        } else {
            // Guest cart
            $cart = Cart::where('session_id', $sessionId)
                ->active()
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'code' => 'crt-' . Str::random(10),
                    'session_id' => $sessionId,
                    'expires_at' => now()->addDays(7), // Guest carts expire
                ]);
            }
            
            return $cart;
        }
    }

    /**
     * Add item to cart.
     */
    public function addItem(int $productId, ?int $variantId = null, array $addonIds = [], int $quantity = 1, ?string $notes = null): CartItem
    {
        $cart = $this->getActiveCart();
        $product = Product::findOrFail($productId);
        
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            // Verify variant belongs to product
            if ($variant->product_id !== $product->id) {
                throw new \Exception('Variant mismatch');
            }
        }

        // Calculate unit price (snapshot)
        $unitPrice = $variant ? $variant->final_price : $product->base_price;
        
        // Calculate addons price
        $addonsPrice = 0;
        if (!empty($addonIds)) {
            // TODO: Validate addons availability
            $addonsPrice = \App\Models\Addon::whereIn('id', $addonIds)->sum('price');
        }

        // Check availability (optional step depending on strictness)
        if (!$product->is_available) {
             throw new \Exception('Product unavailable');
        }

        // Add or update item
        // Note: For complex items with different addons/notes, we usually just add a new line
        // rather than merging quantity. Simple food apps might merge if exact match.
        // We will separate items if addons or notes differ.

        // Simpler approach for now: Always create new line if addons differ
        // But for exact duplicate (same variant, same addons, same notes), update qty
        
        $existing = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->where('notes', $notes)
            ->get() // Need to check addons in PHP since it's JSON
            ->first(function ($item) use ($addonIds) {
                $currentAddons = $item->addons ?? [];
                sort($currentAddons);
                sort($addonIds);
                return $currentAddons == $addonIds;
            });

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing;
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'addons' => $addonIds, // Array cast to JSON automatically
            'quantity' => $quantity,
            'notes' => $notes,
            'unit_price' => $unitPrice,
            'addons_price' => $addonsPrice,
        ]);
    }

    /**
     * Update item quantity.
     */
    public function updateQuantity(int $itemId, int $quantity): bool
    {
        $cart = $this->getActiveCart();
        $item = $cart->items()->findOrFail($itemId);
        
        if ($quantity <= 0) {
            return $item->delete();
        }
        
        return $item->update(['quantity' => $quantity]);
    }

    /**
     * Remove item.
     */
    public function removeItem(int $itemId): bool
    {
        $cart = $this->getActiveCart();
        return $cart->items()->where('id', $itemId)->delete();
    }

    /**
     * Clear cart.
     */
    public function clearCart(): void
    {
        $cart = $this->getActiveCart();
        $cart->items()->delete();
    }

    /**
     * Get cart details with totals.
     */
    public function getCartDetails(): array
    {
        $cart = $this->getActiveCart();
        $cart->load(['items.product', 'items.variant']);

        return [
            'cart_id' => $cart->id,
            'code' => $cart->code,
            'items_count' => $cart->total_items,
            'subtotal' => $cart->subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
            'items' => $cart->items,
        ];
    }
}
