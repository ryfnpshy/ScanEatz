<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            
            // Snapshot data saat order dibuat (immutable)
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->json('addons_snapshot')->nullable(); // [{id, name, price, qty}, ...]
            $table->integer('quantity');
            $table->integer('unit_price'); // Harga per unit (base + variant adjustment)
            $table->integer('addons_price'); // Total harga addons
            $table->integer('line_total'); // (unit_price + addons_price) * quantity
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('product_id'); // For analytics
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
