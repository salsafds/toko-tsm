<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
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

        $this->apiUrl    = env('MARKETPLACE_API_URL', 'http://127.0.0.1:8001/api/pending-orders');
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
            $exists = Penjualan::where('catatan', 'like', '%' . $orderData['marketplace_order_id'] . '%')->exists();
            if ($exists) {
                $this->warn("Order {$orderData['marketplace_order_id']} sudah pernah di-sync. Skip.");
                continue;
            }

            DB::beginTransaction();
            try {
                // Generate ID Penjualan sendiri di sini
                $idPenjualan = $this->generatePenjualanId();

                // Buat Penjualan
                $penjualan = Penjualan::create([
                    'id_penjualan'         => $idPenjualan,
                    'id_pelanggan'         => $orderData['id_pelanggan'] ?? null,
                    'id_anggota'           => $orderData['id_anggota'] ?? null,
                    'id_user'              => null, // dari marketplace
                    'sumber_transaksi'     => 'marketplace',
                    'tanggal_order'        => $orderData['tanggal_order'],
                    'tanggal_selesai'      => now(), // langsung selesai
                    'diskon_penjualan'     => $orderData['diskon_penjualan'] ?? 0,
                    'total_harga_penjualan'=> $orderData['total_harga_penjualan'],
                    'jenis_pembayaran'     => 'kredit', // sesuai permintaanmu
                    'uang_diterima'        => $orderData['uang_diterima'] ?? $orderData['total_harga_penjualan'],
                    'catatan'              => 'Sync otomatis dari Marketplace - Order ID: ' . $orderData['marketplace_order_id'],
                ]);

                // Proses Detail + Kurangi Stok
                foreach ($orderData['details'] as $detail) {
                    $barang = Barang::where('sku', $detail['sku'])->first();

                    if (!$barang) {
                        throw new \Exception("SKU {$detail['sku']} tidak ditemukan di sistem toko.");
                    }

                    if ($barang->stok < $detail['kuantitas']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak cukup. Tersedia: {$barang->stok}, diminta: {$detail['kuantitas']}");
                    }

                    $subTotal = $barang->retail * $detail['kuantitas'];

                    DetailPenjualan::create([
                        'id_detail_penjualan' => $this->generateDetailId(),
                        'id_penjualan'        => $penjualan->id_penjualan,
                        'id_barang'           => $barang->id_barang,
                        'kuantitas'           => $detail['kuantitas'],
                        'sub_total'           => $subTotal,
                    ]);

                    // Kurangi stok
                    app(BarangController::class)->kurangiStokDariPenjualan(
                        $barang->id_barang,
                        $detail['kuantitas']
                    );
                }

                // Update status di marketplace
                $updateResponse = Http::post($this->updateUrl, [
                    'id'     => $orderData['id_penjualan_marketplace'],
                    'status' => 'synced'
                ]);

                if ($updateResponse->failed()) {
                    throw new \Exception('Gagal update status synced di marketplace.');
                }

                DB::commit();
                $this->info("Berhasil sync order: {$orderData['marketplace_order_id']}");
                $syncedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Gagal sync order {$orderData['marketplace_order_id']}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Sync selesai. Total order baru: {$syncedCount}");
        return 0;
    }

    // Duplikat method generate ID dari PenjualanController
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