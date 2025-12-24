<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Barang; 

class SyncStokToMarketplace extends Command
{
    protected $signature = 'sync:stok-marketplace';
    protected $description = 'Sync stok barang dari toko koperasi ke marketplace';

    protected $updateUrl;

    public function __construct()
    {
        parent::__construct();
        $this->updateUrl = env('MARKETPLACE_STOK_UPDATE_URL', 'http://127.0.0.1:8001/api/update-stok');
    }

    public function handle()
    {
        $barangs = Barang::all(); // Ambil semua barang (atau filter where stok changed jika ada timestamp)

        if ($barangs->isEmpty()) {
            $this->info('Tidak ada barang untuk di-sync.');
            return 0;
        }

        $syncedCount = 0;

        foreach ($barangs as $barang) {
            try {
                $response = Http::post($this->updateUrl, [
                    'sku' => $barang->sku,
                    'stok' => $barang->stok,
                ]);

                if ($response->failed()) {
                    throw new \Exception('Gagal update stok untuk SKU ' . $barang->sku . ': ' . $response->body());
                }

                $this->info("Berhasil sync stok untuk SKU: {$barang->sku} (stok: {$barang->stok})");
                $syncedCount++;
            } catch (\Exception $e) {
                $this->error("Gagal sync stok untuk SKU {$barang->sku}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Sync stok selesai. Total barang di-sync: {$syncedCount}");
        return 0;
    }
}