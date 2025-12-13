<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Terikat dengan user
            $table->string('nama_produk');
            $table->string('foto')->nullable(); // Path foto
            $table->decimal('harga_modal', 15, 2); // Harga modal
            $table->decimal('harga_jual', 15, 2); // Harga jual
            $table->integer('stok')->default(0); // Stok barang
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'nama_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};