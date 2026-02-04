<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Outlet;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories()
    {
        return response()->json([
            'data' => Category::active()->ordered()->get()
        ]);
    }

    public function products(Request $request)
    {
        $query = Product::available();

        if ($request->has('category')) {
            // Find by slug
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $query->inCategory($cat->id);
            }
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('base_price', 'asc'),
                'price_desc' => $query->orderBy('base_price', 'desc'),
                'rating' => $query->orderBy('average_rating', 'desc'),
                default => $query->orderBy('order_count', 'desc'), // Popularity default
            };
        }

        return ProductResource::collection($query->paginate(20));
    }

    public function productDetail(Product $product)
    {
        $outletId = request()->query('outlet_id');
        
        // Eager load relationships
        $product->load(['addons']);
        
        if ($outletId) {
            $product->load(['variants' => fn($q) => $q->where('outlet_id', $outletId)]);
        } else {
             // Just show first outlet's variants or all
             $product->load('variants');
        }

        return new ProductResource($product);
    }

    public function outlets(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if ($lat && $lng) {
            return response()->json([
                'data' => Outlet::near($lat, $lng)->active()->get()
            ]);
        }

        return response()->json([
            'data' => Outlet::active()->get()
        ]);
    }
}
