<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // add-1, add-2
            $table->string('name', 100); // "Keju", "Telur Ceplok", "Extra Sambal"
            $table->integer('price'); // Harga addon dalam rupiah
            $table->integer('max_quantity')->default(5); // Max quantity yang bisa dipilih
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->index('is_available');
        });
        
        // Pivot table: product <-> addons (many-to-many)
        Schema::create('product_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['product_id', 'addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_addon');
        Schema::dropIfExists('addons');
    }
};
