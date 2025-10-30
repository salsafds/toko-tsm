<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('negara')->insert([
            'id_negara' => 'NG001',
            'nama_negara' => 'Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
