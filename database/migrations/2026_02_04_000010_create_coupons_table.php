<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // HEMAT10, DISKON25K
            $table->string('name', 100); // "Diskon 10% Hemat Banget"
            $table->text('description')->nullable();
            $table->enum('type', ['percent', 'fixed']); // percent = %, fixed = nominal rupiah
            $table->integer('value'); // 10 untuk 10%, atau 25000 untuk Rp 25.000
            $table->integer('min_subtotal')->default(0); // Minimum belanja untuk apply kupon
            $table->integer('max_discount')->nullable(); // Max discount untuk type=percent
            $table->integer('usage_limit')->nullable(); // Total usage limit (global)
            $table->integer('usage_per_user')->default(1); // Per-user usage limit
            $table->integer('used_count')->default(0); // Tracking total usage
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index(['is_active', 'valid_from', 'valid_until']);
        });
        
        // Track per-user coupon usage
        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->unique(['coupon_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupons');
    }
};
