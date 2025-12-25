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
            $table->timestamp('tanggal_pembelian');
            $table->timestamp('tanggal_terima')->nullable();
            $table->string('id_supplier', 11);
            $table->string('id_user', 11);
            $table->enum('jenis_pembayaran', ['Cash', 'Transfer', 'Kredit'])->nullable();
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('biaya_pengiriman', 15, 2)->default(0);
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('id_supplier')->references('id_supplier')
                  ->on('supplier')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('id_user')->references('id_user')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};