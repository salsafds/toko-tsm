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
    Schema::create('kategori_pelanggan', function (Blueprint $table) {
        $table->string('id_kategori_pelanggan', 11)->primary();
        $table->string('nama_kategori_pelanggan', 255);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pelanggan');
    }
};
