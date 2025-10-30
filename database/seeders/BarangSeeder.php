<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('barang')->insert([
            // Barang 1 - lengkap
            [
                'id_barang' => 'BRG0001',
                'id_kategori_barang' => 'KB001', // Rokok
                'id_supplier' => 'SP001',        // PT Sumber Makmur
                'nama_barang' => 'Marlboro Merah',
                'id_satuan' => 'ST002',          // pack
                'merk_barang' => 'Marlboro',
                'berat' => 0.25,
                'harga_beli' => 25000.00,
                'stok' => 120,
                'retail' => 30000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Barang 2 - kolom stok, harga beli, retail dikosongkan
            [
                'id_barang' => 'BRG0002',
                'id_kategori_barang' => 'KB002', // Alat tulis
                'id_supplier' => 'SP002',        // CV Berkah Abadi
                'nama_barang' => 'Pulpen Biru Standard AE7',
                'id_satuan' => 'ST001',          // pcs
                'merk_barang' => 'Standard',
                'berat' => 0.02,
                'harga_beli' => null,
                'stok' => null,
                'retail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
