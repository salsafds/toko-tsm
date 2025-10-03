<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id_user', 11)->primary();
            $table->string('nama_lengkap', 255);
            $table->enum('jabatan_karyawan', ['Manager', 'Staff', 'Kasir', 'Gudang'])->nullable(); // bisa disesuaikan
            $table->string('alamat_user', 255);
            $table->string('telepon', 16);
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->binary('foto_user')->nullable();
            $table->enum('jenis_kelamin', ['Perempuan', 'Laki - Laki']);
            $table->enum('status', ['Kontrak', 'Tetap'])->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('role', [
                'admin toko',
                'admin master',
                'admin simpanan',
                'admin pinjaman',
                'admin accounting',
                'pengurus'
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
