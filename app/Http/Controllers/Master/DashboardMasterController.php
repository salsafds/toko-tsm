<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// MODELS
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Barang;

class DashboardMasterController extends Controller
{
    public function index()
    {
        /* ===============================
        | PERIODE (BULAN INI)
        =============================== */
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $periode = $start->translatedFormat('F Y');

        /* ===============================
        | SUMMARY
        =============================== */
        $totalPenjualan = Penjualan::whereBetween('tanggal_order', [$start, $end])
            ->whereNotNull('tanggal_selesai')
            ->count();

        $totalPembelian = Pembelian::whereBetween('tanggal_pembelian', [$start, $end])
            ->whereNotNull('tanggal_terima')
            ->count();
        
        $barangTerjual = DetailPenjualan::join(
                'penjualan',
                'detail_penjualan.id_penjualan',
                '=',
                'penjualan.id_penjualan'
            )
            ->whereBetween('penjualan.tanggal_order', [$start, $end])
            ->whereNotNull('penjualan.tanggal_selesai')
            ->sum('detail_penjualan.kuantitas');


        $pendapatan = Penjualan::whereBetween('tanggal_order', [$start, $end])
            ->whereNotNull('tanggal_selesai')
            ->sum('total_harga_penjualan');

        $pengeluaran = Pembelian::whereBetween('tanggal_pembelian', [$start, $end])
            ->whereNotNull('tanggal_terima')
            ->sum('jumlah_bayar');

        $totalHppTerjual = DetailPenjualan::join(
                'penjualan',
                'detail_penjualan.id_penjualan',
                '=',
                'penjualan.id_penjualan'
            )
            ->join(
                'barang',
                'detail_penjualan.id_barang',
                '=',
                'barang.id_barang'
            )
            ->whereBetween('penjualan.tanggal_order', [$start, $end])
            ->whereNotNull('penjualan.tanggal_selesai')
            ->sum(DB::raw('detail_penjualan.kuantitas * barang.harga_beli'));

        $keuntungan = $pendapatan - $totalHppTerjual;

        $summary = [
            'transaksi'   => $totalPenjualan + $totalPembelian,
            'penjualan'   => $totalPenjualan,
            'pembelian'   => $totalPembelian,
            'barang_terjual' => $barangTerjual,
            'pendapatan'  => $pendapatan,
            'pengeluaran' => $pengeluaran,
            'keuntungan'  => $keuntungan,
        ];

        /* ===============================
        | GRAFIK HARIAN
        =============================== */
        $chartLabels = [];
        $chartPenjualan = [];
        $chartPembelian = [];

        for ($i = 1; $i <= $end->day; $i++) {
            $chartLabels[] = $i;

            $chartPenjualan[] = Penjualan::whereDay('tanggal_order', $i)
                ->whereMonth('tanggal_order', $start->month)
                ->whereYear('tanggal_order', $start->year)
                ->whereNotNull('tanggal_selesai')
                ->sum('total_harga_penjualan');

            $chartPembelian[] = Pembelian::whereDay('tanggal_pembelian', $i)
                ->whereMonth('tanggal_pembelian', $start->month)
                ->whereYear('tanggal_pembelian', $start->year)
                ->whereNotNull('tanggal_terima')
                ->sum('jumlah_bayar');
        }

        $perbandingan = [
            'penjualan' => array_sum($chartPenjualan),
            'pembelian' => array_sum($chartPembelian),
        ];

        /* ===============================
        | TOP DATA (JUMLAH TRANSAKSI)
        =============================== */

        // TOP 5 BARANG TERLARIS (berdasarkan total item terjual)
        $topBarang = DetailPenjualan::select(
                'id_barang',
                DB::raw('SUM(kuantitas) as total')
            )
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->whereBetween('penjualan.tanggal_order', [$start, $end])
            ->whereNotNull('penjualan.tanggal_selesai')
            ->groupBy('id_barang')
            ->orderByDesc('total')
            ->with('barang')
            ->limit(5)
            ->get();


        // TOP SUPPLIER (berdasarkan JUMLAH transaksi pembelian)
        $topSupplier = Pembelian::select(
                'id_supplier',
                DB::raw('COUNT(id_pembelian) as jumlah_transaksi')  // <-- UBAH DI SINI
            )
            ->whereBetween('tanggal_pembelian', [$start, $end])
            ->whereNotNull('tanggal_terima')
            ->groupBy('id_supplier')
            ->orderByDesc('jumlah_transaksi')  // ubah juga di sini
            ->with('supplier')
            ->limit(5)
            ->get();


        // TOP PELANGGAN (berdasarkan JUMLAH transaksi penjualan)
        $topPelanggan = Penjualan::select(
                'id_pelanggan',
                DB::raw('COUNT(id_penjualan) as total')
            )
            ->whereNotNull('id_pelanggan')
            ->whereBetween('tanggal_order', [$start, $end])
            ->whereNotNull('tanggal_selesai')
            ->groupBy('id_pelanggan')
            ->orderByDesc('total')
            ->with('pelanggan')
            ->limit(5)
            ->get();


        // TOP ANGGOTA (berdasarkan JUMLAH transaksi penjualan)
        $topAnggota = Penjualan::select(
                'id_anggota',
                DB::raw('COUNT(id_penjualan) as total')
            )
            ->whereNotNull('id_anggota')
            ->whereBetween('tanggal_order', [$start, $end])
            ->whereNotNull('tanggal_selesai')
            ->groupBy('id_anggota')
            ->orderByDesc('total')
            ->with('anggota')
            ->limit(5)
            ->get();


        $top = [
            'barang'    => $topBarang,
            'supplier'  => $topSupplier,
            'pelanggan' => $topPelanggan,
            'anggota'   => $topAnggota,
        ];

        /* ===============================
        | TRANSAKSI BULANAN
        =============================== */
        $penjualanArr = Penjualan::with(['pelanggan', 'anggota'])
            ->whereBetween('tanggal_order', [$start, $end])
            ->get()
            ->map(function ($p) {
                return [
                    'tanggal' => $p->tanggal_order->format('Y-m-d'),
                    'jenis'   => 'Penjualan',
                    'akun'    => $p->pelanggan->nama_pelanggan
                        ?? $p->anggota->nama_anggota
                        ?? '-',
                    'total'   => $p->total_harga_penjualan,
                    'status'  => $p->tanggal_selesai ? 'selesai' : 'pending',
                ];
            });

        $pembelianArr = Pembelian::with('supplier')
            ->whereBetween('tanggal_pembelian', [$start, $end])
            ->get()
            ->map(function ($p) {
                return [
                    'tanggal' => $p->tanggal_pembelian->format('Y-m-d'),
                    'jenis'   => 'Pembelian',
                    'akun'    => $p->supplier->nama_supplier ?? '-',
                    'total'   => $p->jumlah_bayar,
                    'status'  => $p->tanggal_terima ? 'selesai' : 'pending',
                ];
            });

        $transaksiBulanan = collect()
            ->merge($penjualanArr)
            ->merge($pembelianArr)
            ->sortByDesc('tanggal')
            ->values();

        /* ===============================
        | RETURN VIEW
        =============================== */
        return view('master.dashboard-master', compact(
            'periode',
            'summary',
            'chartLabels',
            'chartPenjualan',
            'chartPembelian',
            'perbandingan',
            'top',
            'transaksiBulanan'
        ));
    }
}
