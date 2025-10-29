<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('anggota')->insert([
            [
                'id_anggota' => 'AG0001',
                'username_anggota' => 'tsm2',
                // password hash dari dump (bcrypt)
                'password_anggota' => '$2y$12$vioNCanSHqcFGSLjLNZ6feUqzjbTh8DUgnyDJa.FgwMXNYvnnTaEO',
                'nama_anggota' => 'Imam Hanafi',
                'jenis_kelamin' => 'L',
                'alamat_anggota' => 'Jl. Petemon Barat No.76 Tambaksari',
                'kota_anggota' => 'surabaya',
                'tempat_lahir' => 'kbj',
                'tanggal_lahir' => '2025-10-03',
                'departemen' => 'PRODUKSI BOPP',
                'pekerjaan' => 'TNI',
                'jabatan' => 'KETUA',
                'agama' => 'KATOLIK',
                'status_perkawinan' => 'BELUM KAWIN',
                'tanggal_registrasi' => '2025-10-26',
                'tanggal_keluar' => null,
                'no_telepon' => '0843447755511',
                'status_anggota' => 'AKTIF',
                'foto' => 'storage/uploads/sjqfUYh2FqxS4XZd3VZu25fCav5s5kCXwAaGPWvC.jpg',
            ],
            [
                'id_anggota' => 'AG0002',
                'username_anggota' => 'fikri',
                'password_anggota' => '$2y$12$MWP.ctkTM1RY2dT8Axzlt.U0ezoyXczi5GP9RzRvPmSYF.GgGLGqC',
                'nama_anggota' => 'Fikri Naki',
                'jenis_kelamin' => 'L',
                'alamat_anggota' => 'Jl. Tidar No.9 Sawahan',
                'kota_anggota' => 'Ponorogo',
                'tempat_lahir' => 'ponorogo',
                'tanggal_lahir' => '2010-03-03',
                'departemen' => null,
                'pekerjaan' => 'KARYAWAN SWASTA',
                'jabatan' => 'BENDAHARA',
                'agama' => null,
                'status_perkawinan' => null,
                'tanggal_registrasi' => '2025-10-29',
                'tanggal_keluar' => null,
                'no_telepon' => '080004111654',
                'status_anggota' => 'AKTIF',
                'foto' => null,
            ],
        ]);
    }
}
