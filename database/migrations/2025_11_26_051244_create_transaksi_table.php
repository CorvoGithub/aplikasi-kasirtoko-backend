<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Terikat dengan user
            $table->string('kode_transaksi')->unique(); // Kode transaksi unik
            $table->decimal('total_harga', 15, 2); // Total harga penjualan
            $table->decimal('uang_diberikan', 15, 2); // Uang yang diberikan pelanggan
            $table->decimal('kembalian', 15, 2); // Kembalian
            $table->text('catatan')->nullable(); // Catatan transaksi
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};