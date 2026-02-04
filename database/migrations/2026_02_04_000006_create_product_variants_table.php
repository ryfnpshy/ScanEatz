<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // var-1, var-2
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100); // "Original", "Level 1", "Level 3", "Porsi Jumbo"
            $table->string('variant_type', 50); // "spice_level", "portion_size", "flavor"
            $table->integer('price_adjustment')->default(0); // +/- dari base_price
            $table->integer('stock')->default(999); // Stock per outlet
            $table->integer('min_stock_alert')->default(10); // Alert jika stock < threshold
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->unique(['product_id', 'outlet_id', 'name']);
            $table->index(['product_id', 'outlet_id', 'is_available']);
            $table->index('stock'); // For stock alerts
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
