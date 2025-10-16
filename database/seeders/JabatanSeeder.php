<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            [
                'id_jabatan' => 'J01',
                'nama_jabatan' => 'Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_jabatan' => 'J02',
                'nama_jabatan' => 'Kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
