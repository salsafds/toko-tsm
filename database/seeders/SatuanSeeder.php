<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('satuan')->insert([
            [
                'id_satuan' => 'ST001',
                'nama_satuan' => 'pcs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_satuan' => 'ST002',
                'nama_satuan' => 'pack',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
