<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'id_user' => 'USR001',
            'nama_lengkap' => 'Admin Utama',
            'jabatan_karyawan' => null,
            'alamat_user' => 'Jl. Mawar No. 1',
            'telepon' => '081234567890',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'foto_user' => null,
            'jenis_kelamin' => 'Laki - Laki',
            'status' => 'Tetap',
            'tanggal_masuk' => '2024-01-01',
            'tanggal_keluar' => null,
            'role' => 'admin master',
        ]);
    }
}
