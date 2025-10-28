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
            [
                'id_user' => 'USR001',
            'nama_lengkap' => 'Admin Master',
            'alamat_user' => 'Jl. Mawar No. 1',
            'telepon' => '081234567890',
            'username' => 'adminm',
            'password' => Hash::make('password123'),
            'foto_user' => null,
            'jenis_kelamin' => 'Laki-laki',
            'status' => 'aktif',
            'tanggal_masuk' => '2024-01-01',
            'tanggal_keluar' => null,
            'id_role' => 'R01',         // FK ke role
            'id_jabatan' => 'J01',      // FK ke jabatan
            'id_pendidikan' => 'PD03',       // FK ke pendidikan (S1)
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'id_user' => 'USR002',
            'nama_lengkap' => 'Admin Toko',
            'alamat_user' => 'Jl. Mawar No. 1',
            'telepon' => '081234567890',
            'username' => 'admint',
            'password' => Hash::make('password123'),
            'foto_user' => null,
            'jenis_kelamin' => 'Perempuan',
            'status' => 'aktif',
            'tanggal_masuk' => '2024-01-01',
            'tanggal_keluar' => null,
            'id_role' => 'R02',         // FK ke role
            'id_jabatan' => 'J02',      // FK ke jabatan
            'id_pendidikan' => 'PD02',       // FK ke pendidikan (S1)
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
    }
}
