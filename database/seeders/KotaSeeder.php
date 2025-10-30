<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kota')->insert([
            [
                'id_kota' => 'KT001',
                'nama_kota' => 'Surabaya',
                'id_negara' => 'NG001',
                'id_provinsi' => 'PV001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kota' => 'KT002',
                'nama_kota' => 'Bandung',
                'id_negara' => 'NG001',
                'id_provinsi' => 'PV002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
