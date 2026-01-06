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

        $period = $request->query('period', 'all');

        return view('master.mutasi.index', [
            'paginated' => $data['paginated'],
            'perPage'   => $data['perPage'],
            'totalRows' => $data['allRows']->count(),
            'period'    => $period
        ]);
    }

    public function export(Request $request)
    {
        $data = $this->getMutasiData($request, true);

        $format     = $request->get('format', 'pdf');
        $period     = $request->get('period', 'all');
        $periodText = $this->getPeriodText($period);

        $nowWIB = Carbon::now('Asia/Jakarta');

        $namaFile = 'Laporan_Mutasi_Barang_' . $periodText . '_' . $nowWIB->format('d-m-Y_H-i');

        if ($format === 'excel') {
            return Excel::download(new class($data['allRows'], $periodText, $nowWIB) 
            implements \Maatwebsite\Excel\Concerns\FromView {
                protected $rows;
                protected $periodText;
                protected $nowWIB;

                public function __construct($rows, $periodText, $nowWIB)
                {
                    $this->rows = $rows;
                    $this->periodText = $periodText;
                    $this->nowWIB = $nowWIB;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view('master.mutasi.export-excel', [
                        'rows'       => $this->rows,
                        'periodText' => $this->periodText,
                        'nowWIB'     => $this->nowWIB
                    ]);
                }
            }, $namaFile . '.xlsx');
        }

        $pdf = PDF::loadView('master.mutasi.export-pdf', [
            'rows'        => $data['allRows'],
            'generatedAt' => $nowWIB->translatedFormat('d F Y H:i'),
            'periodText'  => $periodText
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile . '.pdf');
    }

    private function getPeriodText($period)
    {
        return match ($period) {
            '1w' => '1_Minggu_Terakhir',
            '1m' => '1_Bulan_Terakhir',
            '3m' => '3_Bulan_Terakhir',
            '1y' => '1_Tahun_Terakhir',
            default => 'Semua_Periode',
        };
    }

    private function getMutasiData(Request $request, $allData = false)
    {
        $perPage = $allData ? 999999 : $request->get('per_page', 10);
        $period  = $request->get('period', 'all');

        // Tentukan batas tanggal
        $dateFrom = null;
        if ($period !== 'all') {
            $dateFrom = match ($period) {
                '1w' => Carbon::now()->subWeek(),
                '1m' => Carbon::now()->subMonth(),
                '3m' => Carbon::now()->subMonths(3),
                '1y' => Carbon::now()->subYear(),
                default => null,
            };
        }

        $pembelians = Pembelian::with('detailPembelian.barang')
            ->whereNotNull('tanggal_terima')
            ->get();

        $penjualans = Penjualan::with('detailPenjualan.barang')
            ->whereNotNull('tanggal_selesai')
            ->get();

        $transaksiPerBarang = [];

        // PEMBELIAN (MASUK) — sama persis seperti sebelumnya
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
                    'tanggal' => $pembelian->tanggal_terima,
                    'type'    => 'masuk',
                    'qty'     => $detail->kuantitas,
                    'harga'   => $hargaAkhir,
                    'total'   => $hargaAkhir * $detail->kuantitas,
                    'margin'  => $detail->barang->margin ?? 0,
                ];
            }
        }

        // PENJUALAN (KELUAR)
        foreach ($penjualans as $penjualan) {
            foreach ($penjualan->detailPenjualan as $detail) {
                $transaksiPerBarang[$detail->id_barang][] = [
                    'tanggal' => $penjualan->tanggal_selesai,
                    'type'    => 'keluar',
                    'qty'     => $detail->kuantitas,
                    'harga'   => 0,
                    'total'   => 0,
                    'margin'  => $detail->barang->margin ?? 0,
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

            $calculatedMasuk = collect($transaksis)->where('type', 'masuk')->sum('qty');
            $calculatedKeluar = collect($transaksis)->where('type', 'keluar')->sum('qty');
            $calculatedStokAkhir = $calculatedMasuk - $calculatedKeluar;

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

            foreach ($transaksis as $t) {
                if ($dateFrom && $t['tanggal']->lt($dateFrom) && $t['type'] !== 'initial') {
                    continue;
                }

                $isInitialOutsidePeriod = ($t['type'] === 'initial' && $dateFrom && $t['tanggal']->lt($dateFrom));

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

                if (!$isInitialOutsidePeriod) {
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
        }

        // ================== INI YANG DIUBAH: SORTING DULU, BARU PAGINATION ==================
        $sortColumn    = $request->get('sort', 'tanggal');
        $sortDirection = $request->get('direction', 'asc');

        // Sort collection (dengan secondary sort supaya tetap rapi)
        $collection = collect($rows)->sortBy([
            [$sortColumn, $sortDirection],
            ['nama_barang', 'asc'],
            ['tanggal', 'asc']
        ])->values();

        // Kalau export, langsung return semua data yang sudah terurut
        if ($allData) {
            return ['allRows' => $collection];
        }

        // Pagination setelah sorting
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $perPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $perPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return [
            'paginated' => $paginated,
            'perPage'   => $perPage,
            'allRows'   => $collection
        ];
        // ================================================================================
    }
}