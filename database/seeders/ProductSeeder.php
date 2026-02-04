<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Addon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = Outlet::all();

        // 1. Create Categories
        $catAyam = Category::create(['name' => 'Ayam', 'slug' => 'ayam', 'sort_order' => 1, 'icon' => '🍗', 'image_url' => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?auto=format&fit=crop&w=400&q=80']);
        $catMie = Category::create(['name' => 'Mie', 'slug' => 'mie', 'sort_order' => 2, 'icon' => '🍜', 'image_url' => 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?auto=format&fit=crop&w=400&q=80']);
        $catNasi = Category::create(['name' => 'Nasi', 'slug' => 'nasi', 'sort_order' => 3, 'icon' => '🍚', 'image_url' => '/images/products/kategori-nasi.png']);
        $catMinuman = Category::create(['name' => 'Minuman', 'slug' => 'minuman', 'sort_order' => 4, 'icon' => '🥤', 'image_url' => 'https://images.unsplash.com/photo-1497515114629-f71d768fd07c?auto=format&fit=crop&w=400&q=80']);
        $catDessert = Category::create(['name' => 'Dessert', 'slug' => 'dessert', 'sort_order' => 5, 'icon' => '🍰', 'image_url' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=400&q=80']);

        // 2. Create Addons
        $addKeju = Addon::create(['code' => 'add-1', 'name' => 'Keju Parut', 'price' => 3000, 'max_quantity' => 2]);
        $addTelur = Addon::create(['code' => 'add-2', 'name' => 'Telur Ceplok', 'price' => 5000, 'max_quantity' => 2]);
        $addSambal = Addon::create(['code' => 'add-3', 'name' => 'Extra Sambal', 'price' => 2000, 'max_quantity' => 5]);
        $addBakso = Addon::create(['code' => 'add-4', 'name' => 'Bakso Sapi (2pcs)', 'price' => 4000, 'max_quantity' => 3]);

        // 3. Create Products

        // P1: Nasi Ayam Geprek
        $p1 = Product::create([
            'code' => 'prd-1',
            'category_id' => $catAyam->id,
            'name' => 'Nasi Ayam Geprek',
            'slug' => 'nasi-ayam-geprek',
            'description' => 'Ayam goreng tepung digeprek dengan sambal bawang pedas, disajikan dengan nasi hangat dan lalapan.',
            'base_price' => 23000,
            'average_rating' => 4.6,
            'total_reviews' => 128,
            'order_count' => 450,
            'image_url' => 'https://images.unsplash.com/photo-1626074353765-517a681e40be?auto=format&fit=crop&w=800&q=80',
        ]);
        $p1->addons()->attach([$addKeju->id, $addTelur->id, $addSambal->id]);
        
        // Variants for P1
        foreach ($outlets as $outlet) {
            $p1->variants()->createMany([
                ['outlet_id' => $outlet->id, 'code' => 'p1-v1-'.$outlet->id, 'name' => 'Original', 'variant_type' => 'spice_level', 'price_adjustment' => 0, 'stock' => 50],
                ['outlet_id' => $outlet->id, 'code' => 'p1-v2-'.$outlet->id, 'name' => 'Level 1', 'variant_type' => 'spice_level', 'price_adjustment' => 0, 'stock' => 50],
                ['outlet_id' => $outlet->id, 'code' => 'p1-v3-'.$outlet->id, 'name' => 'Level 2', 'variant_type' => 'spice_level', 'price_adjustment' => 0, 'stock' => 50],
            ]);
        }

        // P2: Mie Goreng Spesial
        $p2 = Product::create([
            'code' => 'prd-2',
            'category_id' => $catMie->id,
            'name' => 'Mie Goreng Spesial',
            'slug' => 'mie-goreng-spesial',
            'description' => 'Mie goreng jawa dengan suwiran ayam, telur, dan sayuran segar.',
            'base_price' => 20000,
            'average_rating' => 4.5,
            'total_reviews' => 95,
            'order_count' => 320,
            'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=800&q=80',
        ]);
        $p2->addons()->attach([$addBakso->id, $addTelur->id, $addSambal->id]);

        foreach ($outlets as $outlet) {
            $p2->variants()->createMany([
                ['outlet_id' => $outlet->id, 'code' => 'p2-v1-'.$outlet->id, 'name' => 'Biasa', 'variant_type' => 'spice_level', 'price_adjustment' => 0, 'stock' => 40],
                ['outlet_id' => $outlet->id, 'code' => 'p2-v2-'.$outlet->id, 'name' => 'Pedas', 'variant_type' => 'spice_level', 'price_adjustment' => 0, 'stock' => 40],
                ['outlet_id' => $outlet->id, 'code' => 'p2-v3-'.$outlet->id, 'name' => 'Jumbo', 'variant_type' => 'size', 'price_adjustment' => 5000, 'stock' => 20],
            ]);
        }

        // P3: Es Teh Manis
        $p3 = Product::create([
            'code' => 'prd-3',
            'category_id' => $catMinuman->id,
            'name' => 'Es Teh Manis',
            'slug' => 'es-teh-manis',
            'description' => 'Teh manis segar dengan es batu kristal.',
            'base_price' => 6000,
            'average_rating' => 4.8,
            'order_count' => 890,
            'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=800&q=80',
        ]);
        
        foreach ($outlets as $outlet) {
            $p3->variants()->createMany([
                ['outlet_id' => $outlet->id, 'code' => 'p3-v1-'.$outlet->id, 'name' => 'Normal', 'variant_type' => 'sugar_level', 'price_adjustment' => 0, 'stock' => 200],
                ['outlet_id' => $outlet->id, 'code' => 'p3-v2-'.$outlet->id, 'name' => 'Less Sugar', 'variant_type' => 'sugar_level', 'price_adjustment' => 0, 'stock' => 100],
            ]);
        }

        // Add 7 more dummy products products to reach 10
        $dummies = [
            ['Nasi Goreng Kampung', $catNasi, 25000, 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=800&q=80'],
            ['Ayam Bakar Madu', $catAyam, 28000, 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=800&q=80'],
            ['Kwetiau Siram Sapi', $catMie, 32000, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?auto=format&fit=crop&w=800&q=80'],
            ['Es Jeruk Peras', $catMinuman, 12000, 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=800&q=80'],
            ['Jus Alpukat', $catMinuman, 15000, 'https://images.unsplash.com/photo-1589733955941-5eeaf752f6dd?auto=format&fit=crop&w=800&q=80'],
            ['Pisang Bakar Keju', $catDessert, 18000, '/images/products/pisang-bakar-keju.png'],
            ['Roti Bakar Coklat', $catDessert, 15000, '/images/products/roti-bakar-coklat.png'],
        ];

        foreach ($dummies as $i => $d) {
            $idx = $i + 4; // Start code from prd-4
            $prod = Product::create([
                'code' => "prd-{$idx}",
                'category_id' => $d[1]->id,
                'name' => $d[0],
                'slug' => \Illuminate\Support\Str::slug($d[0]),
                'description' => "Deskripsi lezat untuk {$d[0]}.",
                'base_price' => $d[2],
                'image_url' => $d[3],
                'average_rating' => 4.0 + (rand(0, 9) / 10),
                'order_count' => rand(10, 200),
            ]);

            // Add default variants
            foreach ($outlets as $outlet) {
                $prod->variants()->create([
                    'outlet_id' => $outlet->id,
                    'code' => "p{$idx}-v1-{$outlet->id}",
                    'name' => 'Standard',
                    'variant_type' => 'standard',
                    'price_adjustment' => 0,
                    'stock' => 50,
                ]);
            }
        }
    }
}
