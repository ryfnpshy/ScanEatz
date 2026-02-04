<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('driver_name')->nullable(); // For MVP, manual entry
            $table->string('driver_phone', 20)->nullable();
            $table->string('vehicle_type', 50)->nullable(); // Motor, Mobil
            $table->string('vehicle_plate', 20)->nullable();
            $table->enum('status', [
                'ASSIGNED', 
                'PICKED_UP', 
                'DELIVERED'
            ])->default('ASSIGNED');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_assignments');
    }
};
