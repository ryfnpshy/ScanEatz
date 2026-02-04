<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->json('addons')->nullable(); // Array of addon IDs, e.g., [1, 3, 5]
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable(); // Catatan khusus: "Level 3, sedikit minyak"
            $table->integer('unit_price'); // Snapshot harga saat ditambahkan
            $table->integer('addons_price')->default(0); // Total harga addons
            $table->timestamps();
            
            $table->index('cart_id');
            $table->index(['product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
