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
    Schema::create('supplier', function (Blueprint $table) {
        $table->string('id_supplier', 11)->primary();
        $table->string('nama_supplier', 255);
        $table->string('alamat', 255);
        $table->string('id_negara', 11);
        $table->string('id_provinsi', 11);
        $table->string('id_kota', 11);
        $table->string('telepon_supplier', 16);
        $table->string('email_supplier', 255)->nullable();
        $table->timestamps();

        $table->foreign('id_negara')->references('id_negara')->on('negara')->onDelete('cascade');
        $table->foreign('id_provinsi')->references('id_provinsi')->on('provinsi')->onDelete('cascade');
        $table->foreign('id_kota')->references('id_kota')->on('kota')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
