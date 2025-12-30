<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAnggotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->string('id_anggota', 20)->primary();
            $table->string('username_anggota', 50)->unique();
            $table->string('password_anggota', 255);
            $table->string('nama_anggota', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat_anggota', 255);
            $table->string('kota_anggota', 100);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('departemen', [
                'PRODUKSI BOPP','PRODUKSI SLITTING','WH','QA','HRD','GA','PURCHASING','ACCOUNTING','ENGINEERING'
            ])->nullable();
            $table->enum('pekerjaan', [
                'TNI','PNS','KARYAWAN SWASTA','GURU','BURUH','TANI','PEDAGANG','WIRASWASTA','MENGURUS RUMAH TANGGA','LAINNYA','PENSIUNAN','PENJAHIT'
            ])->nullable();
            $table->enum('jabatan', ['KETUA','SEKRETARIS','BENDAHARA','PENGAWAS','KARYAWAN','PERUSAHAAN']);
            $table->enum('agama', ['ISLAM','KATOLIK','PROTESTAN','HINDU','BUDHA','LAINNYA'])->nullable();
            $table->enum('status_perkawinan', ['BELUM KAWIN','KAWIN','CERAI HIDUP','CERAI MATI','LAINNYA'])->nullable();
            $table->timestamp('tanggal_registrasi')->useCurrent();
            $table->date('tanggal_keluar')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status_anggota', ['AKTIF','NON AKTIF'])->default('AKTIF');
            $table->string('foto', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anggota');
    }
}
