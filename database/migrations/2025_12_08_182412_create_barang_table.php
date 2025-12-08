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
    Schema::create('barang', function (Blueprint $table) {
        $table->string('id_barang', 30)->primary();
        $table->string('id_kategori_barang', 11);
        $table->string('sku', 15);
        $table->string('id_supplier', 11);
        $table->string('nama_barang', 255);
        $table->string('id_satuan', 11);
        $table->string('merk_barang', 255);
        $table->decimal('berat', 11,2);
        $table->decimal('harga_beli', 10, 2)->default(0);
        $table->decimal('margin', 5, 2)->default(0.00);
        $table->integer('stok')->default(0);
        $table->decimal('retail', 10, 2)->default(0);
        $table->timestamps();

        $table->foreign('id_kategori_barang')->references('id_kategori_barang')->on('kategori_barang')->onDelete('cascade');
        $table->foreign('id_supplier')->references('id_supplier')->on('supplier')->onDelete('cascade');
        $table->foreign('id_satuan')->references('id_satuan')->on('satuan')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
