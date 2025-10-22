<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pendidikan')->insert([
            ['id_pendidikan' => 'PD01', 'tingkat_pendidikan' => 'SMA'],
            ['id_pendidikan' => 'PD02', 'tingkat_pendidikan' => 'D3'],
            ['id_pendidikan' => 'PD03', 'tingkat_pendidikan' => 'S1'],
        ]);
    }
}
