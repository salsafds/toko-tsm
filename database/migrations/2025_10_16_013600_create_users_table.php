<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id_user', 50)->primary();

            $table->string('nama_lengkap', 255);
            $table->string('alamat_user', 255)->nullable();
            $table->string('telepon', 16)->nullable();
            $table->string('username', 100)->unique();
            $table->string('password', 255);
            $table->string('foto_user', 255)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();

            // Foreign keys
            $table->string('id_role', 10)->nullable();
            $table->string('id_jabatan', 11)->nullable();
            $table->string('id_pendidikan')->nullable();

            // Tambahkan constraint FK
            $table->foreign('id_role')
                ->references('id_role')
                ->on('role')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('id_jabatan')
                ->references('id_jabatan')
                ->on('jabatan')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('id_pendidikan')
                ->references('id_pendidikan')
                ->on('pendidikan')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
