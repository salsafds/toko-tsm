<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('supplier')->insert([
            [
                'id_supplier' => 'SP001',
                'nama_supplier' => 'PT Sumber Makmur',
                'alamat' => 'Jl. Raya Industri No. 15',
                'id_negara' => 'NG001',      // Indonesia
                'id_provinsi' => 'PV001',   // Jawa Timur
                'id_kota' => 'KT001',        // Surabaya
                'telepon_supplier' => '081234567890',
                'email_supplier' => 'info@sumbermakmur.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_supplier' => 'SP002',
                'nama_supplier' => 'CV Berkah Abadi',
                'alamat' => 'Jl. Sukajadi No. 23',
                'id_negara' => 'NG001',      // Indonesia
                'id_provinsi' => 'PV002',   // Jawa Barat
                'id_kota' => 'KT002',        // Bandung
                'telepon_supplier' => '082112345678',
                'email_supplier' => 'berkah.abadi@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
