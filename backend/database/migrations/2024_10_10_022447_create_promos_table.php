<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product'); // Menyimpan ID produk terkait
            $table->string('title');
            $table->string('banner');// Judul promo
            $table->text('description'); // Deskripsi promo
            $table->date('start_date'); // Tanggal mulai promo
            $table->date('end_date'); // Tanggal akhir promo
            $table->integer('discount_percentage')->default(0); // Persentase diskon
            $table->timestamps();

            // Menambahkan foreign key untuk id_product
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
