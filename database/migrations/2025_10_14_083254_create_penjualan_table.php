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
    Schema::create('penjualan', function (Blueprint $table) {
        $table->string('id_penjualan', 11)->primary();
        $table->string('id_pelanggan', 11);
        $table->time('tanggal_penjualan');
        $table->integer('diskon_penjualan');
        $table->integer('total_harga_penjualan');
        $table->integer('jenis_pembayaran');
        $table->timestamps();

        $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
