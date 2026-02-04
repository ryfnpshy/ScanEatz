<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;

class Cart extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $cartId;

    protected $listeners = ['cart-updated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        // Must resolve service from container or passed in method
        $cartService = app(CartService::class);
        $cart = $cartService->getActiveCart();
        $this->cartId = $cart->id;
        
        // Eager load for view
        $cart->load(['items.product', 'items.variant']);
        
        $this->cartItems = $cart->items;
        $this->subtotal = $cart->subtotal;
    }

    public function updateQuantity($itemId, $qty)
    {
        $cartService = app(CartService::class);
        $cartService->updateQuantity($itemId, max(0, $qty));
        
        $this->dispatch('cart-updated');
        $this->loadCart();
    }

    public function removeItem($itemId)
    {
        $cartService = app(CartService::class);
        $cartService->removeItem($itemId);
        
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Item dihapus dari keranjang.');
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart')->layout('layouts.app');
    }
}
