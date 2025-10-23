<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id_pelanggan', 11)->primary();
            $table->string('nama_pelanggan', 255);
            $table->string('nomor_telepon', 16)->nullable();
            $table->enum('kategori_pelanggan', ['badan_usaha', 'perorangan', 'pelanggan_umum']);
            $table->string('email_pelanggan', 255)->nullable();
            $table->string('id_negara', 11);
            $table->string('id_provinsi', 11);
            $table->string('id_kota', 11);
            $table->timestamps();

            $table->foreign('id_negara')->references('id_negara')->on('negara')->onDelete('cascade');
            $table->foreign('id_provinsi')->references('id_provinsi')->on('provinsi')->onDelete('cascade');
            $table->foreign('id_kota')->references('id_kota')->on('kota')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
