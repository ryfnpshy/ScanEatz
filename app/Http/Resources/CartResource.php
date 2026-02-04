<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_code' => $this->code,
            'subtotal' => $this->subtotal,
            'items_count' => $this->total_items,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'addons_price' => $item->addons_price,
                    'line_total' => $item->line_total,
                    'notes' => $item->notes,
                    'addons' => $item->addons, // Raw IDs for now
                    'image_url' => $item->product->getFirstMediaUrl('images') ?: 'https://placehold.co/100?text=IMG',
                ];
            }),
        ];
    }
}
