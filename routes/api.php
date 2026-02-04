<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Catalog Routes
Route::prefix('catalog')->group(function () {
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/products', [CatalogController::class, 'products']);
    Route::get('/products/{product}', [CatalogController::class, 'productDetail']);
    Route::get('/outlets', [CatalogController::class, 'outlets']);
});

// Cart Routes (Guest & Auth)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'show']);
    Route::post('/add', [CartController::class, 'add']);
    Route::put('/items/{itemId}', [CartController::class, 'update']);
    Route::delete('/items/{itemId}', [CartController::class, 'remove']);
});

// Checkout & Orders (Auth Required for Orders, but Checkout can serve guest flow if needed)
// For ScanEatz, let's assume Checkout requires valid session/user context handled by Service
Route::prefix('checkout')->group(function () {
    Route::post('/', [CheckoutController::class, 'process']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/{order}/cancel', [OrderController::class, 'cancel']);
    });
});

// Tracking (Public with Order Code)
Route::get('/tracking/{code}', [OrderController::class, 'track']);
