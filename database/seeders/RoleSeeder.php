<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role')->insert([
            [
                'id_role' => 'R01',
                'nama_role' => 'Admin Master',
                'keterangan' => 'Akses penuh ke semua fitur sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_role' => 'R02',
                'nama_role' => 'Kasir',
                'keterangan' => 'Akses untuk transaksi penjualan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_role' => 'R03',
                'nama_role' => 'Karyawan',
                'keterangan' => 'Akses untuk input dan laporan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
