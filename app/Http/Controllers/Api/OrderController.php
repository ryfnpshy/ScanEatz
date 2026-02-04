<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['outlet', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        // Authorization check
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new OrderResource($order);
    }

    public function track($code)
    {
        $order = Order::where('order_code', $code)->firstOrFail();
        
        // Public tracking allowed via code
        return new OrderResource($order);
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['reason' => 'required|string|min:5']);

        try {
            $this->orderService->cancelOrder($order, $request->reason, Auth::id());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
