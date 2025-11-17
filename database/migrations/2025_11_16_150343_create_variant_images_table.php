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
        Schema::create('variant_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_variant_id');
            $table->string('image_path'); // Path ke storage
            $table->boolean('is_primary')->default(false); // Cek apakah gambar utama / tidak
            $table->integer('order')->default(0); // Urutan tampilan
            $table->timestamps();

            // Index
            $table->index(['shoe_variant_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_images');
    }
};
