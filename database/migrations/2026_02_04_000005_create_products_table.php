<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // prd-1, prd-2
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('base_price'); // Harga dasar dalam rupiah (integer, no decimals)
            $table->decimal('average_rating', 3, 2)->default(0.00); // 0.00 - 5.00
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_halal')->default(true);
            $table->boolean('is_vegetarian')->default(false);
            $table->integer('preparation_time_minutes')->default(15); // Estimasi waktu masak
            $table->integer('view_count')->default(0); // For tracking popularity
            $table->integer('order_count')->default(0); // Total penjualan
            $table->string('image_url')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'is_available']);
            $table->index('slug');
            $table->index(['is_available', 'average_rating']); // For filtering & sorting
            $table->index('order_count'); // For "Best Sellers"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
