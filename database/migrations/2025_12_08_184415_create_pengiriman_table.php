<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->string('id_pengiriman', 11)->primary();
            $table->string('id_agen_ekspedisi', 11);
            $table->string('id_penjualan', 11);
            $table->string('nomor_resi', 255)->unique()->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->integer('total_berat_bruto')->nullable();
            $table->string('nama_penerima', 255);
            $table->string('telepon_penerima', 20);
            $table->text('alamat_penerima');
            $table->string('kode_pos', 10);
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_agen_ekspedisi')->references('id_ekspedisi')->on('agen_ekspedisi');
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};