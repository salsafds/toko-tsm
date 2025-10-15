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
            $table->string('nomor_resi', 255)->unique();
            $table->integer('biaya_pengiriman');
            $table->string('status_pembayaran', 255);
            $table->decimal('diskon_biaya_kirim', 10, 2)->default(0);
            $table->integer('total_berat_bruto');
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
