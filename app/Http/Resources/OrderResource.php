<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'status' => $this->status,
            'status_label' => $this->status_display,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'fulfillment_type' => $this->fulfillment_type,
            'outlet' => [
                'name' => $this->outlet->name,
                'phone' => $this->outlet->phone,
            ],
            'totals' => [
                'subtotal' => $this->subtotal,
                'delivery_fee' => $this->delivery_fee,
                'discount' => $this->discount_amount,
                'tax' => $this->tax_amount,
                'total' => $this->total_amount,
                'currency' => 'IDR',
            ],
            'items' => $this->items->map(fn($item) => [
                'name' => $item->product_name,
                'variant' => $item->variant_name,
                'quantity' => $item->quantity,
                'total' => $item->line_total,
                'notes' => $item->notes,
                'addons' => $item->addons_snapshot, // Assuming this is cast to array
            ]),
            'timeline' => $this->status_timeline,
            'eta_minutes' => $this->eta_minutes,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
