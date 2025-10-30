<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            // Pelanggan badan usaha
            [
                'id_pelanggan' => 'PLG001',
                'nama_pelanggan' => 'PT Nusantara Jaya',
                'nomor_telepon' => '081398765432',
                'kategori_pelanggan' => 'badan_usaha',
                'email_pelanggan' => 'kontak@nusantarajaya.com',
                'alamat_pelanggan' => 'Jl. Merdeka No. 100',
                'id_negara' => 'NG001',
                'id_provinsi' => 'PV002', // Jawa Barat
                'id_kota' => 'KT002',      // Bandung
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pelanggan umum (dummy data untuk transaksi tanpa data pribadi)
            [
                'id_pelanggan' => 'PLG002',
                'nama_pelanggan' => 'Pelanggan Umum',
                'nomor_telepon' => null,
                'kategori_pelanggan' => 'pelanggan_umum',
                'email_pelanggan' => null,
                'alamat_pelanggan' => '-',
                'id_negara' => 'NG001',
                'id_provinsi' => 'PV001', // Jawa Timur
                'id_kota' => 'KT001',      // Surabaya
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
