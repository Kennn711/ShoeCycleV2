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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('recipient_name');       // nama_penerima
            $table->string('phone_number', 20);     // nomor_handphone

            // Informasi Alamat Fisik
            $table->enum('label', ['Home', 'Office', 'Apartment', 'Boarding House', 'Other']); // label_alamat (Rumah, Kantor, dll)
            $table->text('full_address');           // alamat   _lengkap (Jalan, RT/RW, No. Rumah)
            $table->string('district');         // Kecamatan (PENTING: Magersari, Kranggan, Prajurit Kulon, dll)
            $table->string('village')->nullable(); // Kelurahan/Desa


            // Fitur Tambahan
            $table->text('courier_note')->nullable(); // catatan_untuk_kurir (Pagar warna hitam, titip satpam)
            $table->boolean('is_primary')->default(false); // Alamat utama/default
            $table->decimal('latitude', 10, 8)->nullable(); // Untuk Driver (Google Maps)
            $table->decimal('longitude', 11, 8)->nullable(); // Untuk Driver (Google Maps)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
