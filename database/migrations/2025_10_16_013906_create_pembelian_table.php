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
            $table->date('tanggal_pembelian');
            $table->timestamp('tanggal_terima')->nullable();
            $table->string('id_supplier', 11);
            $table->string('id_user', 11); 
            $table->enum('jenis_pembayaran', ['cash', 'kredit']); 
            $table->decimal('jumlah_bayar', 10, 2);
            $table->timestamps();

            $table->foreign('id_supplier')->references('id_supplier')->on('supplier');
            $table->foreign('id_user')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};