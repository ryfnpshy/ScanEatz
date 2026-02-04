<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_code', 30)->unique(); // PAY-2026-0001
            $table->enum('method', [
                'COD',           // Cash on Delivery
                'BANK_TRANSFER', // Manual bank transfer
                'E_WALLET',      // GoPay, OVO, Dana, etc.
                'CREDIT_CARD',   // Future expansion
            ]);
            $table->integer('amount'); // Jumlah yang harus dibayar
            $table->enum('status', [
                'PENDING',   // Menunggu pembayaran
                'PAID',      // Sudah dibayar
                'FAILED',    // Gagal
                'REFUNDED'   // Refund (untuk cancel)
            ])->default('PENDING');
            $table->string('external_id')->nullable(); // Payment gateway transaction ID
            $table->string('payment_url')->nullable(); // Deep link untuk e-wallet
            $table->json('metadata')->nullable(); // Data dari payment gateway
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable(); // Payment expiration (15 min)
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('external_id');
            $table->index('expired_at'); // For timeout jobs
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
