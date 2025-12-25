<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Barang;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /* =========================
        | 1️⃣ SUMMARY (SELESAI SAJA)
        ========================== */

        $totalPendapatanHariIni = Penjualan::whereDate('tanggal_order', $today)
            ->whereNotNull('tanggal_selesai')
            ->sum('total_harga_penjualan');

        $totalPenjualanHariIni = Penjualan::whereDate('tanggal_order', $today)
            ->whereNotNull('tanggal_selesai')
            ->count();

        $totalTransaksiHariIni =
            Penjualan::whereDate('tanggal_order', $today)
                ->whereNotNull('tanggal_selesai')
                ->count()
            +
            Pembelian::whereDate('tanggal_pembelian', $today)
                ->whereNotNull('tanggal_terima')
                ->count();

        $totalBarangTerjualHariIni = DetailPenjualan::whereHas('penjualan', function ($q) use ($today) {
                $q->whereDate('tanggal_order', $today)
                  ->whereNotNull('tanggal_selesai');
            })
            ->sum('kuantitas');

        $totalPembeliHariIni = Penjualan::whereDate('tanggal_order', $today)
            ->whereNotNull('tanggal_selesai')
            ->where(function ($q) {
                $q->whereNotNull('id_pelanggan')
                  ->orWhereNotNull('id_anggota');
            })
            ->count();

        /* =========================
        | 2️⃣ GRAFIK PER JAM
        ========================== */

        $chartLabels = range(0, 23);
        $chartPenjualan = array_fill(0, 24, 0);
        $chartPembelian = array_fill(0, 24, 0);

        $penjualanPerJam = Penjualan::selectRaw('HOUR(tanggal_order) jam, SUM(total_harga_penjualan) total')
            ->whereDate('tanggal_order', $today)
            ->whereNotNull('tanggal_selesai')
            ->groupBy('jam')
            ->get();

        foreach ($penjualanPerJam as $row) {
            $chartPenjualan[$row->jam] = (int) $row->total;
        }

        $pembelianPerJam = Pembelian::selectRaw('HOUR(tanggal_pembelian) jam, SUM(jumlah_bayar) total')
            ->whereDate('tanggal_pembelian', $today)
            ->whereNotNull('tanggal_terima')
            ->groupBy('jam')
            ->get();

        foreach ($pembelianPerJam as $row) {
            $chartPembelian[$row->jam] = (int) $row->total;
        }

        /* =========================
        | 3️⃣ TRANSAKSI TERBARU (ARRAY MURNI)
        ========================== */

        $penjualan = Penjualan::with(['pelanggan', 'anggota'])
            ->whereDate('tanggal_order', $today)
            ->get();

        $penjualanArr = $penjualan->map(function ($p) {
            return [
                'tanggal' => $p->tanggal_order->format('Y-m-d H:i'),
                'jenis'   => 'Penjualan',
                'akun'    => $p->pelanggan->nama_pelanggan
                              ?? $p->anggota->nama_anggota
                              ?? '-',
                'total'   => $p->total_harga_penjualan,
                'status'  => $p->tanggal_selesai ? 'selesai' : 'pending',
            ];
        });

        $pembelian = Pembelian::with('supplier')
            ->whereDate('tanggal_pembelian', $today)
            ->get();

        $pembelianArr = $pembelian->map(function ($p) {
            return [
                'tanggal' => $p->tanggal_pembelian->format('Y-m-d H:i'),
                'jenis'   => 'Pembelian',
                'akun'    => $p->supplier->nama_supplier ?? '-',
                'total'   => $p->jumlah_bayar,
                'status'  => $p->tanggal_terima ? 'selesai' : 'pending',
            ];
        });

        $transaksiTerbaru = collect()
            ->merge($penjualanArr)
            ->merge($pembelianArr)
            ->sortByDesc('tanggal')
            ->take(5)
            ->values()
            ->toArray();

        /* =========================
        | 4️⃣ RIWAYAT TRANSAKSI (ARRAY MURNI)
        ========================== */

        $riwayatTransaksiHariIni = collect()
            ->merge($penjualanArr)
            ->merge($pembelianArr)
            ->sortByDesc('tanggal')
            ->values()
            ->toArray();

        /* =========================
        | 5️⃣ STATUS STOK
        ========================== */

        $barangStokHabis = Barang::where('stok', '<=', 0)->count();
        $daftarBarangStokHabis = Barang::where('stok', '<=', 0)->get();

        $barangStokMenipis = Barang::whereBetween('stok', [1, 5])->count();
        $daftarBarangStokMenipis = Barang::whereBetween('stok', [1, 5])->get();

        $barangTerlarisHariIni = DetailPenjualan::selectRaw('id_barang, SUM(kuantitas) total')
            ->whereHas('penjualan', fn ($q) =>
                $q->whereDate('tanggal_order', $today)
                  ->whereNotNull('tanggal_selesai')
            )
            ->groupBy('id_barang')
            ->with('barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard-admin', compact(
            'totalPendapatanHariIni',
            'totalPenjualanHariIni',
            'totalTransaksiHariIni',
            'totalBarangTerjualHariIni',
            'totalPembeliHariIni',
            'chartLabels',
            'chartPenjualan',
            'chartPembelian',
            'transaksiTerbaru',
            'riwayatTransaksiHariIni',
            'barangStokHabis',
            'barangStokMenipis',
            'barangTerlarisHariIni',
            'daftarBarangStokHabis',
            'daftarBarangStokMenipis'
        ));
    }
}
