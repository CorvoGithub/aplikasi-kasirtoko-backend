<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->integer('qty'); // Kuantitas barang
            $table->decimal('harga_satuan', 15, 2); // Harga saat transaksi
            $table->decimal('subtotal', 15, 2); // Qty * Harga satuan
            $table->timestamps();

            // Index untuk performa
            $table->index(['transaksi_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};