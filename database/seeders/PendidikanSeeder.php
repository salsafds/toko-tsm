<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pendidikan')->insert([
            ['id_pendidikan' => 1, 'tingkat_pendidikan' => 'SMA'],
            ['id_pendidikan' => 2, 'tingkat_pendidikan' => 'D3'],
            ['id_pendidikan' => 3, 'tingkat_pendidikan' => 'S1'],
        ]);
    }
}
