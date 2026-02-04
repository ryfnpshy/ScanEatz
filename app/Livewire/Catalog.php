<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;

class Catalog extends Component
{
    use WithPagination;

    public $categorySlug = 'all';
    public $search = '';
    public $sortBy = 'popularity';
    public $activeFilters = []; // halal, vegetarian

    protected $queryString = [
        'categorySlug' => ['except' => 'all', 'as' => 'category'],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'popularity'],
    ];

    public function mount($category = 'all')
    {
        $this->categorySlug = $category;
    }

    public function setCategory($slug)
    {
        $this->categorySlug = $slug;
        $this->resetPage();
    }

    public function addToCart(int $productId, CartService $cartService)
    {
        try {
            // For simple products without mandatory variants/addons
            // In a real app, this would open a modal if the product has options
            $product = Product::find($productId);
            
            if ($product->variants()->count() > 0 || $product->addons()->count() > 0) {
                 // Dispatch event to open Product Detail Modal
                 $this->dispatch('open-product-modal', productId: $productId); 
                 return;
            }

            $cartService->addItem($productId);
            $this->dispatch('cart-updated'); // Update header cart count
            $this->dispatch('notify', message: 'Berhasil ditambahkan ke keranjang!');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menambahkan: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $categories = cache()->remember('active_categories', 60, function () {
            return Category::ordered()->active()->get();
        });
        
        $query = Product::with('category')->available();

        if ($this->categorySlug !== 'all') {
            $category = $categories->where('slug', $this->categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($this->search) {
            $query->search($this->search);
        }

        match ($this->sortBy) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'rating' => $query->orderBy('average_rating', 'desc'),
            default => $query->orderBy('order_count', 'desc'),
        };

        return view('livewire.catalog', [
            'products' => $query->paginate(12),
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
