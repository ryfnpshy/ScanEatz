<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'average_rating',
        'total_reviews',
        'is_available',
        'is_halal',
        'is_vegetarian',
        'preparation_time_minutes',
        'view_count',
        'order_count',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'average_rating' => 'decimal:2',
            'total_reviews' => 'integer',
            'is_available' => 'boolean',
            'is_halal' => 'boolean',
            'is_vegetarian' => 'boolean',
            'preparation_time_minutes' => 'integer',
            'view_count' => 'integer',
            'order_count' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get available addons for this product.
     */
    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'product_addon')
            ->withTimestamps();
    }

    /**
     * Get reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get variants for a specific outlet.
     */
    public function variantsForOutlet(int $outletId)
    {
        return $this->variants()
            ->where('outlet_id', $outletId)
            ->where('is_available', true)
            ->get();
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment order count.
     */
    public function incrementOrderCount(int $quantity = 1): void
    {
        $this->increment('order_count', $quantity);
    }

    /**
     * Update average rating.
     */
    public function updateAverageRating(): void
    {
        $this->average_rating = $this->reviews()
            ->where('is_published', true)
            ->avg('rating') ?? 0;
        
        $this->total_reviews = $this->reviews()
            ->where('is_published', true)
            ->count();
        
        $this->save();
    }

    /**
     * Scope to get available products.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter halal products.
     */
    public function scopeHalal(Builder $query): Builder
    {
        return $query->where('is_halal', true);
    }

    /**
     * Scope to filter vegetarian products.
     */
    public function scopeVegetarian(Builder $query): Builder
    {
        return $query->where('is_vegetarian', true);
    }

    /**
     * Scope to get best sellers.
     */
    public function scopeBestSellers(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('order_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get highly rated products.
     */
    public function scopeHighlyRated(Builder $query, float $minRating = 4.0): Builder
    {
        return $query->where('average_rating', '>=', $minRating)
            ->where('total_reviews', '>', 0);
    }

    /**
     * Scope to search products.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get formatted price in Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }
}
