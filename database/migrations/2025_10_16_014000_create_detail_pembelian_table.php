<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->string('id_detail_pembelian')->primary(); 
            $table->string('id_pembelian'); 
            $table->string('id_barang'); 
            $table->decimal('harga_beli', 15, 2); 
            $table->integer('kuantitas'); 
            $table->decimal('sub_total', 15, 2); 
            $table->timestamps();


            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
