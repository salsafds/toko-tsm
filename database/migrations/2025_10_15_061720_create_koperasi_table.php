<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasi', function (Blueprint $table) {
            $table->string('nama_koperasi', 255)->primary();
            $table->string('npwp', 255);
            $table->string('alamat_koperasi', 255);
            $table->string('telepon_koperasi', 255);
            $table->string('email_koperasi', 255);
            $table->string('fax_koperasi', 255);
            $table->string('kode_pos', 11);
            $table->string('website', 255);
            $table->binary('logo_koperasi')->nullable();
            $table->string('nama_pimpinan', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi');
    }
};
