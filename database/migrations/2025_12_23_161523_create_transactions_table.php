<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('address_id')->constrained('addresses');

            // Penugasan Kurir Lokal (Nullable karena diisi setelah dibayar/diproses)
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');

            // Identitas Transaksi
            $table->string('invoice')->unique(); // ID unik untuk Midtrans (order_id)

            // Detail Biaya
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('admin_fee', 12, 2);
            $table->decimal('total_price', 12, 2);

            // Integrasi Midtrans
            $table->string('snap_token')->nullable(); // Token untuk pop-up Snap
            $table->string('payment_type')->nullable(); // bank_transfer, gopay, qris, dll
            $table->string('pdf_url')->nullable();      // Link instruksi bayar VA (PENTING!)
            $table->enum('payment_status', ['pending', 'settlement', 'expire', 'cancel', 'deny'])
                ->default('pending');

            // Tracking Kurir Lokal
            $table->enum('transaction_status', ['pending', 'processing', 'shipping', 'delivered', 'failed'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
