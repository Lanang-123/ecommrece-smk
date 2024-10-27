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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cart');
            $table->unsignedBigInteger('id_user');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 100)->nullable();
            $table->enum('metode_pembayaran', ['transfer', 'cash']);
            $table->string('nama_akun', 100)->nullable();
            $table->string('no_inv', 100)->nullable();
            $table->enum('status_konfirmasi', ['selesai', 'menunggu']);
            $table->integer('total', )->default(0);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_cart')->references('id')->on('carts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};