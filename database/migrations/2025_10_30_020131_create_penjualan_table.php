<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->string('id_penjualan', 11)->primary();
            $table->string('id_pelanggan', 11);
            $table->string('id_anggota', 11)->nullable();
            $table->timestamp('tanggal_order')->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->integer('diskon_penjualan')->default(0);
            $table->integer('total_harga_penjualan');
            $table->enum('jenis_pembayaran', ['tunai', 'kredit']);
            $table->timestamps();

            // FK pelanggan
            $table->foreign('id_pelanggan')
                  ->references('id_pelanggan')
                  ->on('pelanggan')
                  ->onDelete('cascade');

            // FK anggota 
            $table->foreign('id_anggota')
                  ->references('id_anggota')
                  ->on('anggota')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
