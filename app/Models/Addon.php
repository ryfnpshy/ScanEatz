<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'price',
        'max_quantity',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'max_quantity' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get products that can have this addon.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_addon')
            ->withTimestamps();
    }

    /**
     * Scope to get available addons.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
