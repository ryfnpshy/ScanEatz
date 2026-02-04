<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderTracking extends Component
{
    public $orderCode;
    public $order;
    public $timeline = [];

    public function mount($code)
    {
        $this->orderCode = $code;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['items', 'outlet', 'payment', 'deliveryAssignment'])
            ->where('order_code', $this->orderCode)
            ->firstOrFail();

        // Security check: if user is logged in, ensure they own the order OR just rely on code secrecy (common for food apps)
        // Ideally: strict auth if logged in.
        if (Auth::check() && $this->order->user_id && $this->order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $this->timeline = $this->order->status_timeline ?? [];
    }

    public function render()
    {
        return view('livewire.order-tracking')->layout('layouts.app');
    }
}
