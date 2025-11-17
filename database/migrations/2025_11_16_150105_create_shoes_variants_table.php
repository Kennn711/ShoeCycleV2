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
        Schema::create('shoes_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_id');
            $table->string('color', 50);
            $table->integer('size');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('sku', 50)->unique()->nullable(); // SKU unik per varian
            $table->boolean('is_available')->default(true); // Tersedia / tidak
            $table->timestamps();

            // Index untuk performa query
            $table->index(['shoe_id', 'color', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoes_variants');
    }
};
