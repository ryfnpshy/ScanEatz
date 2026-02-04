<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Catalog;
use App\Livewire\Cart;
use App\Livewire\Checkout;

Route::get('/', function () {
    return view('home');
});

Route::get('/catalog', Catalog::class)->name('catalog');
Route::get('/cart', Cart::class)->name('cart');

// Auth routes should naturally be here via Breeze/Jetstream if installed,
// but for now we focus on the core app routes.

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout');
    // Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
});

Route::get('/tracking/{code}', \App\Livewire\OrderTracking::class)->name('order.tracking');

// require __DIR__.'/auth.php';
