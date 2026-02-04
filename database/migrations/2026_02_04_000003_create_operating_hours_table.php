<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Sunday, 1=Monday, ..., 6=Saturday
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_closed')->default(false); // Untuk hari libur
            $table->timestamps();
            
            $table->unique(['outlet_id', 'day_of_week']);
            $table->index('day_of_week');
        });
        
        // Table untuk blackout dates (hari libur nasional, dll)
        Schema::create('outlet_blackout_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->date('blackout_date');
            $table->string('reason')->nullable(); // "Tahun Baru", "Lebaran", dll
            $table->timestamps();
            
            $table->unique(['outlet_id', 'blackout_date']);
            $table->index('blackout_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlet_blackout_dates');
        Schema::dropIfExists('operating_hours');
    }
};
