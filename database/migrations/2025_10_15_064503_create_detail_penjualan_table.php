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
    Schema::create('detail_penjualan', function (Blueprint $table) {
        $table->string('id_detail_penjualan', 11)->primary();
        $table->string('id_penjualan', 11);
        $table->string('id_barang', 30);
        $table->integer('kuantitas');
        $table->integer('sub_total');
        $table->timestamps();

        $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('cascade');
        $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};