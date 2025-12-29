<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Http\Controllers\Admin\BarangController;

class SyncMarketplaceOrders extends Command
{
    protected $signature = 'sync:marketplace';
    protected $description = 'Sync orders dari Marketplace ke database toko koperasi';

    protected $apiUrl;
    protected $updateUrl;

    public function __construct()
    {
        parent::__construct();

        $this->apiUrl    = env('MARKETPLACE_API_URL', 'http://127.0.0.1:8001/api/complete-orders');
        $this->updateUrl = env('MARKETPLACE_UPDATE_URL', 'http://127.0.0.1:8001/api/update-sync');
    }

    public function handle()
    {
        $response = Http::get($this->apiUrl);

        if ($response->failed()) {
            $this->error('Gagal mengambil data dari API Marketplace: ' . $response->body());
            return 1;
        }

        $orders = $response->json();

        if (empty($orders)) {
            $this->info('Tidak ada order baru dari marketplace.');
            return 0;
        }

        $syncedCount = 0;

        foreach ($orders as $orderData) {
            // Skip kalau sudah pernah di-sync
            $exists = Penjualan::where('catatan', 'like', '%' . $orderData['id_penjualan'] . '%')->exists();
            if ($exists) {
                $this->line("Order {$orderData['id_penjualan']} sudah pernah di-sync, dilewati.");
                continue;
            }

            DB::beginTransaction();
            try {
                $idPenjualan = $this->generatePenjualanId();

                // Hitung total seperti di PenjualanController@store
                $total_dpp = 0;
                $total_non_ppn = 0;
                $itemsProcessed = [];

                foreach ($orderData['details'] as $detail) {
                    $barang = Barang::where('sku', $detail['sku'])->first();
                    if (!$barang) {
                        throw new \Exception("SKU {$detail['sku']} tidak ditemukan di sistem toko.");
                    }
                    if ($barang->stok < $detail['kuantitas']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak cukup. 
                        Tersedia: {$barang->stok}, diminta: {$detail['kuantitas']}");
                    }

                    $sub_total_item = $barang->retail * $detail['kuantitas'];

                    if (strtolower($barang->kena_ppn ?? '') === 'ya') {
                        $total_dpp += $sub_total_item;
                    } else {
                        $total_non_ppn += $sub_total_item;
                    }

                    $itemsProcessed[] = [
                        'barang' => $barang,
                        'kuantitas' => $detail['kuantitas'],
                        'sub_total' => $sub_total_item,
                    ];
                }

                
                $diskonPersen = 0;
                $tarif_ppn = 11;
                $biayaPengiriman = 0;

                $subTotalBarangDanOngkir = $total_dpp + $total_non_ppn + $biayaPengiriman;
                $diskonNilai = $subTotalBarangDanOngkir * ($diskonPersen / 100);
                $dppSetelahDiskon = $total_dpp - ($total_dpp * $diskonPersen / 100);
                $total_ppn = round($dppSetelahDiskon * $tarif_ppn / 100);
                $totalHarga = $subTotalBarangDanOngkir - $diskonNilai + $total_ppn;

                // Create Penjualan
                $penjualan = Penjualan::create([
                    'id_penjualan' => $idPenjualan,
                    'id_pelanggan' => null,
                    'id_anggota' => null,
                    'id_user' => null,
                    'sumber_transaksi' => 'marketplace',
                    'tanggal_order' => $orderData['waktu_transaksi'],
                    'tanggal_selesai' => now(),
                    'diskon_penjualan' => $diskonPersen,
                    'tarif_ppn' => $tarif_ppn,
                    'total_dpp' => round($dppSetelahDiskon),
                    'total_ppn' => $total_ppn,
                    'total_non_ppn' => $total_non_ppn + $biayaPengiriman,
                    'total_harga_penjualan' => $totalHarga,
                    'jenis_pembayaran' => 'kredit',
                    'uang_diterima' => $totalHarga,
                    'catatan' => 'Sync otomatis dari Marketplace - Order ID: ' . $orderData['id_penjualan'],
                ]);

                // Create detail + kurangi stok
                foreach ($itemsProcessed as $item) {
                    DetailPenjualan::create([
                        'id_detail_penjualan' => $this->generateDetailId(),
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_barang' => $item['barang']->id_barang,
                        'kuantitas' => $item['kuantitas'],
                        'sub_total' => $item['sub_total'],
                    ]);

                    app(BarangController::class)->kurangiStokDariPenjualan(
                        $item['barang']->id_barang,
                        $item['kuantitas']
                    );
                }

                // Commit sync lokal 
                DB::commit();

                $this->info("Berhasil sync order lokal: {$orderData['id_penjualan']}");

                // Update Status
                try {
                    $updateResponse = Http::post($this->updateUrl, [
                        'id' => $orderData['id_penjualan'],
                        'status' => 'synced'
                    ]);

                    if ($updateResponse->failed()) {
                        $this->warn("Gagal update status synced di marketplace untuk order {$orderData['id_penjualan']}: HTTP {$updateResponse->status()} - {$updateResponse->body()}");
                        Log::warning('Marketplace sync status failed', [
                            'order_id' => $orderData['id_penjualan'],
                            'response' => $updateResponse->body(),
                            'status_code' => $updateResponse->status(),
                        ]);
                    } else {
                        $this->line("Status synced berhasil dikirim ke marketplace untuk order {$orderData['id_penjualan']}.");
                    }
                } catch (\Exception $e) {
                    $this->warn("Error saat update status marketplace untuk order {$orderData['id_penjualan']}: " . $e->getMessage());
                    Log::error('Marketplace update error', [
                        'order_id' => $orderData['id_penjualan'],
                        'error' => $e->getMessage(),
                    ]);
                }

                $syncedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Gagal sync order {$orderData['id_penjualan']}: " . $e->getMessage());
                // Hanya rollback jika gagal di proses lokal (SKU, stok, dll)
            }
        }

        $this->newLine();
        $this->info("Sync selesai. Total order baru yang berhasil disync lokal: {$syncedCount}");
        return 0;
    }

    private function generatePenjualanId()
    {
        $maxNum = Penjualan::max(DB::raw('CAST(SUBSTRING(id_penjualan, 3) AS UNSIGNED)')) ?? 0;
        return 'PJ' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateDetailId()
    {
        $maxNum = DetailPenjualan::max(DB::raw('CAST(SUBSTRING(id_detail_penjualan, 3) AS UNSIGNED)')) ?? 0;
        return 'DP' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }
}