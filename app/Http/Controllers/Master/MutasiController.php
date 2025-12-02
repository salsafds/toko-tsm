<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // 1. Ambil semua pembelian yang sudah diterima
        $pembelians = Pembelian::with('detailPembelian.barang')
            ->whereNotNull('tanggal_terima')
            ->get();

        // 2. Ambil semua penjualan yang sudah selesai
        $penjualans = Penjualan::with('detailPenjualan.barang')
            ->whereNotNull('tanggal_selesai')
            ->get();

        // Kumpulkan semua transaksi per barang (masuk & keluar)
        $transaksiPerBarang = [];

        // ─────── PEMBELIAN (MASUK) ───────
        foreach ($pembelians as $pembelian) {
            $totalSubTotal   = $pembelian->detailPembelian->sum('sub_total');
            $nilaiDiskon     = ($pembelian->diskon / 100) * $totalSubTotal;
            $setelahDiskon   = $totalSubTotal - $nilaiDiskon;
            $nilaiPpn        = ($pembelian->ppn / 100) * $setelahDiskon;
            $totalKuantitas  = $pembelian->detailPembelian->sum('kuantitas');
            $biayaPengiriman = $pembelian->biaya_pengiriman;

            foreach ($pembelian->detailPembelian as $detail) {
                $proporsi = $totalKuantitas > 0 ? $detail->kuantitas / $totalKuantitas : 0;
                $biayaPerUnit = $biayaPengiriman * $proporsi / $detail->kuantitas;

                $hargaSetelahDiskon = $detail->harga_beli * (1 - $pembelian->diskon / 100);
                $hargaSetelahPpn    = $hargaSetelahDiskon * (1 + $pembelian->ppn / 100);
                $hargaAkhir         = $hargaSetelahPpn + $biayaPerUnit;

                $transaksiPerBarang[$detail->id_barang][] = [
                    'tanggal'   => $pembelian->tanggal_terima,
                    'type'      => 'masuk',
                    'qty'       => $detail->kuantitas,
                    'harga'     => $hargaAkhir,
                    'total'     => $hargaAkhir * $detail->kuantitas,
                    'margin'    => $detail->barang->margin ?? 0,
                ];
            }
        }

        // ─────── PENJUALAN (KELUAR) ───────
        foreach ($penjualans as $penjualan) {
            foreach ($penjualan->detailPenjualan as $detail) {
                $transaksiPerBarang[$detail->id_barang][] = [
                    'tanggal'   => $penjualan->tanggal_selesai,
                    'type'      => 'keluar',
                    'qty'       => $detail->kuantitas,
                    'harga'     => 0, // akan diisi average saat proses running
                    'total'     => 0,
                    'margin'    => $detail->barang->margin ?? 0,
                ];
            }
        }

        // ─────── PROSES RUNNING BALANCE MULAI DARI 0 + HANDLE STOK AWAL JIKA ADA ───────
        $rows = [];

        foreach ($transaksiPerBarang as $id_barang => $transaksis) {
            $barang = Barang::find($id_barang);
            if (!$barang) continue;

            // Urutkan semua transaksi berdasarkan tanggal (ascending)
            usort($transaksis, fn($a, $b) => $a['tanggal'] <=> $b['tanggal']);

            // Mulai dari stok running = 0 dan nilai running = 0
            $stokRunning = 0;
            $nilaiRunning = 0;

            // Hitung stok akhir yang diharapkan dari transaksi saja
            $calculatedMasuk = 0;
            $calculatedKeluar = 0;
            foreach ($transaksis as $t) {
                if ($t['type'] === 'masuk') {
                    $calculatedMasuk += $t['qty'];
                } else {
                    $calculatedKeluar += $t['qty'];
                }
            }
            $calculatedStokAkhir = $calculatedMasuk - $calculatedKeluar;

            // Jika stok di tabel barang lebih besar dari calculated stok akhir, berarti ada stok awal initial
            // Tambahkan entry 'initial' di awal dengan tanggal sebelum transaksi pertama (atau gunakan tanggal pertama minus 1 hari)
            $stokAwalInitial = (int) $barang->stok - $calculatedStokAkhir;
            if ($stokAwalInitial > 0) {
                // Asumsi harga initial = barang->harga_beli (atau 0 jika tidak ada)
                $hargaInitial = $barang->harga_beli ?? 0;
                $totalInitial = $hargaInitial * $stokAwalInitial;

                // Tanggal initial: jika ada transaksi, ambil tanggal pertama minus 1 hari, else now
                $tanggalInitial = !empty($transaksis) ? $transaksis[0]['tanggal']->subDay() : now();

                // Tambah entry initial di awal array transaksi
                array_unshift($transaksis, [
                    'tanggal'   => $tanggalInitial,
                    'type'      => 'initial', // type khusus untuk stok awal
                    'qty'       => $stokAwalInitial,
                    'harga'     => $hargaInitial,
                    'total'     => $totalInitial,
                    'margin'    => $barang->margin ?? 0,
                ]);
            } elseif ($stokAwalInitial < 0) {
                // Jika negative, ini error (mungkin data inconsistent), tapi untuk safety set ke 0 dan log atau abaikan
                // Di sini kita set stokAwalInitial = 0 untuk hindari minus
                $stokAwalInitial = 0;
            }

            // Sekarang proses running balance dari awal
            foreach ($transaksis as $t) {
                $stokSebelum = $stokRunning;
                $averageSebelum = $stokRunning > 0 ? $nilaiRunning / $stokRunning : 0;

                if ($t['type'] === 'masuk' || $t['type'] === 'initial') {
                    $masuk = $t['qty'];
                    $keluar = 0;

                    $nilaiRunning += $t['total']; // tambah nilai masuk/initial
                    $stokRunning += $masuk;
                } else {
                    $masuk = 0;
                    $keluar = $t['qty'];

                    // Set harga dan total untuk keluar berdasarkan average sebelum
                    $t['harga'] = $averageSebelum;
                    $t['total'] = $averageSebelum * $keluar;

                    $nilaiRunning -= $t['total'];
                    $stokRunning -= $keluar;
                }

                $averageSekarang = $stokRunning > 0 ? $nilaiRunning / $stokRunning : 0;

                $rows[] = [
                    'tanggal'       => $t['tanggal'],
                    'nama_barang'   => $barang->nama_barang,
                    'kuantitas'     => $t['qty'],
                    'harga_beli'    => $t['type'] === 'keluar' ? $averageSebelum : $t['harga'],
                    'total_harga'   => $t['type'] === 'keluar' ? $t['total'] : $t['total'],
                    'margin'        => $t['margin'],
                    'average_price' => $averageSekarang,
                    'stok_awal'     => $stokSebelum,
                    'masuk'         => $masuk,
                    'keluar'        => $keluar,
                    'saldo_akhir'   => $stokRunning,
                    'total_amount'  => $stokRunning * $averageSekarang,
                ];
            }

            // Pastikan di akhir, stokRunning == barang->stok (untuk consistency check, bisa tambah log jika tidak match)
        }

        // Urutkan global (barang → tanggal)
        usort($rows, function ($a, $b) {
            if ($a['nama_barang'] === $b['nama_barang']) {
                return $a['tanggal'] <=> $b['tanggal'];
            }
            return $a['nama_barang'] <=> $b['nama_barang'];
        });

        // Pagination
        $collection   = collect($rows);
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $perPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginated    = new LengthAwarePaginator($perPageItems, $collection->count(), $perPage);
        $paginated->setPath($request->url());

        return view('master.mutasi.index', compact('paginated', 'perPage'));
    }
}