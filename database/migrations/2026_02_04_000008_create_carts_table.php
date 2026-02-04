<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // c-1, c-2
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id', 100)->nullable(); // For guest users
            $table->timestamp('expires_at')->nullable(); // Cart expiration (e.g., 7 days)
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
