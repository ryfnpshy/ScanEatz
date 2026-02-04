<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show()
    {
        $cart = $this->cartService->getActiveCart();
        return new CartResource($cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'qty' => 'required|integer|min:1',
            'addons' => 'array',
            'notes' => 'nullable|string|max:200',
        ]);

        try {
            $item = $this->cartService->addItem(
                $request->product_id,
                $request->variant_id,
                $request->addons ?? [],
                $request->qty,
                $request->notes
            );
            
            // Reload cart to get totals
            $cart = $this->cartService->getActiveCart();

            return response()->json([
                'status' => 'ok',
                'message' => 'Item added to cart',
                'data' => new CartResource($cart)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:0'
        ]);

        $this->cartService->updateQuantity($itemId, $request->qty);

        return response()->json([
            'status' => 'ok',
            'data' => new CartResource($this->cartService->getActiveCart())
        ]);
    }

    public function remove($itemId)
    {
        $this->cartService->removeItem($itemId);

        return response()->json([
            'status' => 'ok',
            'data' => new CartResource($this->cartService->getActiveCart())
        ]);
    }
}
