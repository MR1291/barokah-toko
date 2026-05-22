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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id(); // Ini akan jadi nomor nota otomatis (#1, #2, dst)
            $table->integer('total_harga'); // Menyimpan total belanja nota kasir
            $table->integer('bayar')->nullable(); // Jumlah uang yang dibayarkan pembeli
            $table->integer('kembali')->nullable(); // Uang kembalian
            $table->timestamps(); // Mengisi otomatis kolom 'created_at' dan 'updated_at' (Buat tracking tanggal)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};