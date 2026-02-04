<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // otl-1, otl-2
            $table->string('name');
            $table->text('address');
            $table->string('district', 100);
            $table->string('city', 100)->default('Jakarta Pusat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('delivery_radius_km', 5, 2)->default(7.00); // Radius layanan delivery
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('base_eta_minutes')->default(30); // Base ETA untuk delivery
            $table->timestamps();
            
            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
