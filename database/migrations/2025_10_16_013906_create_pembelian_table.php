<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->string('id_pembelian', 11)->primary();
            $table->string('nomor_po', 30);
            $table->date('tanggal_pembelian');
            $table->date('tanggal_kirim')->nullable();
            $table->timestamp('tanggal_terima')->nullable();
            $table->string('id_supplier', 11);
            $table->string('id_user', 11);
            $table->string('jenis_pembayaran', 255);
            $table->decimal('jumlah_bayar', 10, 2);
            $table->timestamps();

            $table->foreign('id_supplier')->references('id_supplier')->on('supplier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};