<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

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

        $penjualanPerJam = Penjualan::selectRaw('HOUR(tanggal_selesai) jam, SUM(total_harga_penjualan) total')
            ->whereDate('tanggal_selesai', $today)
            ->groupBy('jam')
            ->get();

        foreach ($penjualanPerJam as $row) {
            $chartPenjualan[$row->jam] = (int) $row->total;
        }

        $pembelianPerJam = DB::table('pembelian')
            ->join('detail_pembelian', 'pembelian.id_pembelian', '=', 'detail_pembelian.id_pembelian')
            ->selectRaw('HOUR(pembelian.tanggal_terima) as jam')
            ->addSelect([DB::raw('SUM(detail_pembelian.sub_total) as total')])
            ->whereDate('pembelian.tanggal_terima', $today)
            ->whereNotNull('pembelian.tanggal_terima')
            ->groupByRaw('HOUR(pembelian.tanggal_terima)')
            ->get();

        foreach ($pembelianPerJam as $row) {
            $chartPembelian[(int) $row->jam] = (float) $row->total;
        }
        

        /* =========================
        | 3️⃣ TRANSAKSI TERBARU (ARRAY MURNI)
        ========================== */

        // Penjualan: Ambil yang selesai hari ini ATAU (pending DAN order hari ini)
        $penjualanSelesai = Penjualan::with(['pelanggan', 'anggota'])
            ->whereDate('tanggal_selesai', $today)
            ->get();

        $penjualanPending = Penjualan::with(['pelanggan', 'anggota'])
            ->whereNull('tanggal_selesai')
            ->whereDate('tanggal_order', $today)
            ->get();

        $penjualan = $penjualanSelesai->merge($penjualanPending);

        $penjualanArr = $penjualan->map(function ($p) {
            $effectiveDate = $p->tanggal_selesai ?? $p->tanggal_order;
            return [
                'tanggal' => $effectiveDate->format('Y-m-d H:i'),
                'effective_date' => $effectiveDate,  // Untuk sort internal
                'jenis'   => 'Penjualan',
                'akun'    => $p->pelanggan->nama_pelanggan ?? $p->anggota->nama_anggota ?? '-',
                'total'   => $p->total_harga_penjualan,
                'status'  => $p->tanggal_selesai ? 'selesai' : 'pending',
            ];
        });

        // Pembelian: Sama seperti atas
        $pembelianDiterima = Pembelian::with('supplier')
            ->whereDate('tanggal_terima', $today)
            ->get();

        $pembelianPending = Pembelian::with('supplier')
            ->whereNull('tanggal_terima')
            ->whereDate('tanggal_pembelian', $today)
            ->get();

        $pembelian = $pembelianDiterima->merge($pembelianPending);

        $pembelianArr = $pembelian->map(function ($p) {
            $effectiveDate = $p->tanggal_terima ?? $p->tanggal_pembelian;
            return [
                'tanggal' => $effectiveDate->format('Y-m-d H:i'),
                'effective_date' => $effectiveDate,  // Untuk sort
                'jenis'   => 'Pembelian',
                'akun'    => $p->supplier->nama_supplier ?? '-',
                'total'   => $p->jumlah_bayar,
                'status'  => $p->tanggal_terima ? 'selesai' : 'pending',
            ];
        });

        // Merge dan sort by effective_date desc
        $transaksiTerbaru = collect()
            ->merge($penjualanArr)
            ->merge($pembelianArr)
            ->sortByDesc('effective_date')
            ->take(5)
            ->map(function ($item) {
                unset($item['effective_date']);  // Hapus field internal
                return $item;
            })
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
