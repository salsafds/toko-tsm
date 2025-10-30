<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_barang')->insert([
            [
                'id_kategori_barang' => 'KB001',
                'nama_kategori' => 'Rokok',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_barang' => 'KB002',
                'nama_kategori' => 'Alat Tulis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
