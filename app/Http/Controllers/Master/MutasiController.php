<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getMutasiData($request);

        // Untuk tampilan (dengan pagination)
        return view('master.mutasi.index', [
            'paginated' => $data['paginated'],
            'perPage'   => $data['perPage'],
            'totalRows' => $data['allRows']->count()
        ]);
    }

    public function export(Request $request)
    {
        $data = $this->getMutasiData($request, true); // true = ambil semua data tanpa paginasi

        $format = $request->get('format', 'pdf');
        $namaFile = 'Laporan_Mutasi_Barang_' . Carbon::now()->format('d-m-Y_H-i');

        if ($format === 'excel') {
            return Excel::download(new class($data['allRows']) implements \Maatwebsite\Excel\Concerns\FromView {
                protected $rows;

                public function __construct($rows)
                {
                    $this->rows = $rows;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view('master.mutasi.export-excel', [
                        'rows' => $this->rows
                    ]);
                }
            }, $namaFile . '.xlsx');
        }

        // Default: PDF
        $pdf = PDF::loadView('master.mutasi.export-pdf', [
            'rows'       => $data['allRows'],
            'generatedAt'=> Carbon::now()->translatedFormat('d F Y H:i')
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile . '.pdf');
    }

    // ===================================================================
    // FUNGSI UTAMA UNTUK MENGHASILKAN DATA MUTASI (dipakai index & export)
    // ===================================================================
    private function getMutasiData(Request $request, $allData = false)
    {
        $perPage = $allData ? 999999 : $request->get('per_page', 25);

        // 1. Ambil semua pembelian yang sudah diterima
        $pembelians = Pembelian::with('detailPembelian.barang')
            ->whereNotNull('tanggal_terima')
            ->get();

        // 2. Ambil semua penjualan yang sudah selesai
        $penjualans = Penjualan::with('detailPenjualan.barang')
            ->whereNotNull('tanggal_selesai')
            ->get();

        $transaksiPerBarang = [];

        // PEMBELIAN (MASUK)
        foreach ($pembelians as $pembelian) {
            $totalSubTotal   = $pembelian->detailPembelian->sum('sub_total');
            $nilaiDiskon     = ($pembelian->diskon / 100) * $totalSubTotal;
            $setelahDiskon   = $totalSubTotal - $nilaiDiskon;
            $nilaiPpn        = ($pembelian->ppn / 100) * $setelahDiskon;
            $totalKuantitas  = $pembelian->detailPembelian->sum('kuantitas');
            $biayaPengiriman = $pembelian->biaya_pengiriman ?? 0;

            foreach ($pembelian->detailPembelian as $detail) {
                $proporsi = $totalKuantitas > 0 ? $detail->kuantitas / $totalKuantitas : 0;
                $biayaPerUnit = $biayaPengiriman * $proporsi / ($detail->kuantitas ?: 1);

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

        // PENJUALAN (KELUAR)
        foreach ($penjualans as $penjualan) {
            foreach ($penjualan->detailPenjualan as $detail) {
                $transaksiPerBarang[$detail->id_barang][] = [
                    'tanggal'   => $penjualan->tanggal_selesai,
                    'type'      => 'keluar',
                    'qty'       => $detail->kuantitas,
                    'harga'     => 0,
                    'total'     => 0,
                    'margin'    => $detail->barang->margin ?? 0,
                ];
            }
        }

        $rows = [];

        foreach ($transaksiPerBarang as $id_barang => $transaksis) {
            $barang = Barang::find($id_barang);
            if (!$barang) continue;

            usort($transaksis, fn($a, $b) => $a['tanggal'] <=> $b['tanggal']);

            $stokRunning = 0;
            $nilaiRunning = 0;

            // Hitung stok akhir dari transaksi saja
            $calculatedMasuk = collect($transaksis)->where('type', 'masuk')->sum('qty');
            $calculatedKeluar = collect($transaksis)->where('type', 'keluar')->sum('qty');
            $calculatedStokAkhir = $calculatedMasuk - $calculatedKeluar;

            // Stok awal (initial)
            $stokAwalInitial = (int) $barang->stok - $calculatedStokAkhir;
            if ($stokAwalInitial > 0) {
                $hargaInitial = $barang->harga_beli ?? 0;
                $tanggalInitial = !empty($transaksis) ? $transaksis[0]['tanggal']->copy()->subDay() : now();

                array_unshift($transaksis, [
                    'tanggal' => $tanggalInitial,
                    'type'    => 'initial',
                    'qty'     => $stokAwalInitial,
                    'harga'   => $hargaInitial,
                    'total'   => $hargaInitial * $stokAwalInitial,
                    'margin'  => $barang->margin ?? 0,
                ]);
            }

            // Proses running balance
            foreach ($transaksis as $t) {
                $stokSebelum = $stokRunning;
                $averageSebelum = $stokRunning > 0 ? $nilaiRunning / $stokRunning : 0;

                if (in_array($t['type'], ['masuk', 'initial'])) {
                    $masuk  = $t['qty'];
                    $keluar = 0;
                    $nilaiRunning += $t['total'];
                    $stokRunning += $masuk;
                } else {
                    $masuk  = 0;
                    $keluar = $t['qty'];
                    $t['harga'] = $averageSebelum;
                    $t['total'] = $averageSebelum * $keluar;

                    $nilaiRunning -= $t['total'];
                    $stokRunning -= $keluar;
                }

                $averageSekarang = $stokRunning > 0 ? $nilaiRunning / $stokRunning : 0;

                $rows[] = [
                    'tanggal'        => $t['tanggal'],
                    'nama_barang'    => $barang->nama_barang,
                    'keterangan'     => $t['type'] === 'initial' ? 'Stok Awal' : ($t['type'] === 'masuk' ? 'Pembelian' : 'Penjualan'),
                    'kuantitas'      => $t['qty'],
                    'harga_beli'     => round($t['type'] === 'keluar' ? $averageSebelum : $t['harga'], 2),
                    'total_harga'    => round($t['total'], 2),
                    'margin'         => $t['margin'],
                    'average_price'  => round($averageSekarang, 2),
                    'stok_awal'      => $stokSebelum,
                    'masuk'          => $masuk ?? 0,
                    'keluar'         => $keluar ?? 0,
                    'saldo_akhir'    => $stokRunning,
                    'nilai_stok'     => round($stokRunning * $averageSekarang, 2),
                ];
            }
        }

        // Urutkan: nama barang → tanggal
        usort($rows, fn($a, $b) =>
            $a['nama_barang'] === $b['nama_barang']
                ? $a['tanggal'] <=> $b['tanggal']
                : $a['nama_barang'] <=> $b['nama_barang']
        );

        $collection = collect($rows);

        // Jika export, kembalikan semua data
        if ($allData) {
            return ['allRows' => $collection];
        }

        // Jika tampil di web (pagination)
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $perPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated    = new LengthAwarePaginator($perPageItems, $collection->count(), $perPage);
        $paginated->setPath($request->url());

        return [
            'paginated' => $paginated,
            'perPage'   => $perPage,
            'allRows'   => $collection
        ];
    }
}