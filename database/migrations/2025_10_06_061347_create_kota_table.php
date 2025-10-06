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
        Schema::create('kota', function (Blueprint $table) {
            $table->string('id_kota', 11)->primary();
            $table->string('nama_kota', 255);
            $table->string('id_negara', 11);
            $table->string('id_provinsi', 11);

            $table->foreign('id_negara')->references('id_negara')->on('negara')->onDelete('cascade');
            $table->foreign('id_provinsi')->references('id_provinsi')->on('provinsi')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kota');
    }
};
