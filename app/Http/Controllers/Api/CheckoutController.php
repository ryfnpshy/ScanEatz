<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function process(Request $request)
    {
        // For MVP, we might allow Guest checkout, but let's assume User for now or check header
        // If Auth::user() is null, we might create a ghost user or require login.
        // The Service expects a User model.
        
        $user = Auth::user();
        if (!$user) {
             return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'outlet_id' => 'required|exists:outlets,id',
            'fulfillment' => 'required|in:delivery,pickup',
            'address_id' => 'required_if:fulfillment,delivery|exists:addresses,id',
            'payment_method' => 'required|string',
        ]);

        try {
            $order = $this->orderService->checkout($user, $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat.',
                'data' => new OrderResource($order),
                // Return payment instructions URL or data if needed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400); // Bad Request for domain errors like Stock/Radius
        }
    }
}
