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
                'keterangan' => 'Akses data konfigurasi sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_role' => 'R02',
                'nama_role' => 'Admin Toko',
                'keterangan' => 'Akses transaksi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
