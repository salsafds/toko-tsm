<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provinsi')->insert([
            [
                'id_provinsi' => 'PV001',
                'nama_provinsi' => 'Jawa Timur',
                'id_negara' => 'NG001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_provinsi' => 'PV002',
                'nama_provinsi' => 'Jawa Barat',
                'id_negara' => 'NG001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
