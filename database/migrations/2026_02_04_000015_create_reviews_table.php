<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->boolean('is_published')->default(true); // Moderasi
            $table->timestamps();
            
            // Satu review per product per order
            $table->unique(['user_id', 'product_id', 'order_id']);
            $table->index(['product_id', 'is_published', 'created_at']);
            $table->index('rating'); // For filtering
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
