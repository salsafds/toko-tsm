<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            JabatanSeeder::class,
            PendidikanSeeder::class,
            UserSeeder::class,
            NegaraSeeder::class,
            ProvinsiSeeder::class,
            KotaSeeder::class,
            KategoriBarangSeeder::class,
            SatuanSeeder::class,
            SupplierSeeder::class,
            PelangganSeeder::class,
            BarangSeeder::class,
        ]);
    }
}
