<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 50)->default('Rumah'); // Rumah, Kantor, Apartemen, dll
            $table->text('full_address');
            $table->string('district', 100); // Kecamatan
            $table->string('city', 100)->default('Jakarta Pusat');
            $table->string('province', 100)->default('DKI Jakarta');
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7); // For delivery radius calculation
            $table->decimal('longitude', 10, 7);
            $table->text('notes')->nullable(); // Patokan: "Dekat Indomaret, rumah cat hijau"
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'is_default']);
            $table->index(['latitude', 'longitude']); // For geospatial queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
