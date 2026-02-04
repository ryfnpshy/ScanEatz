<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique(); // ORD-2026-0001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('outlet_id')->constrained();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            
            // Fulfillment
            $table->enum('fulfillment_type', ['delivery', 'pickup'])->default('delivery');
            $table->timestamp('scheduled_at')->nullable(); // Untuk order dijadwalkan
            
            // Status state machine
            $table->enum('status', [
                'PENDING',      // Baru dibuat, menunggu pembayaran
                'CONFIRMED',    // Pembayaran confirmed, masuk antrian
                'COOKING',      // Sedang dimasak
                'READY',        // Siap untuk delivery/pickup
                'ON_DELIVERY',  // Dalam pengiriman (untuk delivery only)
                'COMPLETED',    // Selesai
                'CANCELLED'     // Dibatalkan
            ])->default('PENDING');
            
            $table->json('status_timeline')->nullable(); // Array of {status, timestamp}
            
            // Pricing breakdown
            $table->integer('subtotal'); // Subtotal items
            $table->integer('tax_amount')->default(0); // Pajak
            $table->integer('delivery_fee')->default(0); // Ongkir
            $table->integer('discount_amount')->default(0); // Diskon dari kupon
            $table->integer('total_amount'); // Total akhir
            
            // ETA & Timing
            $table->integer('eta_minutes')->nullable(); // Estimasi waktu pengiriman/pickup
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cooking_started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Cancellation
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('merchant_notes')->nullable(); // Internal notes
            
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index('order_code');
            $table->index(['outlet_id', 'status', 'created_at']); // Critical untuk dashboard outlet
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
