<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
            'base_price' => $this->base_price,
            // 'formatted_price' => $this->formatted_price,
            'rating' => $this->average_rating,
            'image_url' => 'https://placehold.co/400?text=' . urlencode($this->name), // Placeholder
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'price_adjustment' => $variant->price_adjustment,
                        'final_price' => $variant->final_price,
                        'is_available' => $variant->is_available,
                    ];
                });
            }),
            'addons' => $this->whenLoaded('addons', function () {
                return $this->addons->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'price' => $a->price,
                ]);
            }),
        ];
    }
}
