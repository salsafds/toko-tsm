<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BulananController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        switch ($periode) {
            case '7hari':
                $start = now()->subDays(6)->startOfDay();
                $end = now()->endOfDay();
                $periodeTeks = $start->format('j') . ' – ' . $end->format('j F Y');
                break;

            case '1bulan_terakhir':
                $start = now()->subDays(29)->startOfDay();
                $end = now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;

            case '3bulan':
                $start = now()->subMonths(2)->startOfMonth();
                $end = now()->endOfMonth();
                $periodeTeks = $start->translatedFormat('F') . ' – ' . $end->translatedFormat('F Y');
                break;

            case 'bulanan':
                $bulan = $request->filled('bulan')
                    ? Carbon::createFromFormat('Y-m', $request->bulan)
                    : now();
                $start = $bulan->copy()->startOfMonth()->startOfDay();
                $end = $bulan->copy()->endOfMonth()->endOfDay();
                $periodeTeks = $bulan->translatedFormat('F Y');
                break;

            case 'tahunan':
                $tahun = (int) $request->get('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end = Carbon::create($tahun)->endOfYear();
                $periodeTeks = $tahun;
                break;

            default:
                $periode = 'bulanan';
                $start = now()->startOfMonth()->startOfDay();
                $end = now()->endOfMonth()->endOfDay();
                $periodeTeks = now()->translatedFormat('F Y');
        }


        $omzet = DB::table('penjualan')
            ->whereBetween('tanggal_selesai', [$start, $end])
            ->sum('total_harga_penjualan');

        $hpp = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->sum(DB::raw('dp.kuantitas * b.harga_beli'));

        $labaKotor = $omzet - $hpp;
        $marginPersen = $omzet > 0 ? round(($labaKotor / $omzet) * 100, 2) : 0;

        $totalPembelian = DB::table('pembelian')
            ->whereBetween('tanggal_pembelian', [$start, $end])
            ->sum('jumlah_bayar');

        $pembelianMasuk = DB::table('pembelian')
            ->whereNotNull('tanggal_terima')
            ->whereBetween('tanggal_terima', [$start, $end])
            ->sum('jumlah_bayar');

        $nilaiStokAkhir = DB::table('barang')->sum(DB::raw('stok * harga_beli'));

        $stokKritis = DB::table('barang')
            ->where('stok', '>', 0)
            ->where('stok', '<', 10)
            ->orderBy('stok')
            ->get(['id_barang', 'nama_barang', 'stok']);

        $terlaris = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select('b.id_barang', 'b.nama_barang', DB::raw('SUM(dp.kuantitas) as qty'), DB::raw('SUM(dp.sub_total) as omzet'))
            ->groupBy('b.id_barang', 'b.nama_barang')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $jenis = $request->get('jenis', 'all');
        $sortTanggal = $request->get('sort_tanggal', 'desc');
        $perPage = (int) $request->get('per_page', 15);

        $penjualanQuery = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->leftJoin('anggota as a', 'p.id_anggota', '=', 'a.id_anggota')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id_user')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select(
                DB::raw('p.tanggal_selesai as tanggal'),
                'p.id_penjualan as id',
                DB::raw("'penjualan' as jenis"),
                DB::raw('COALESCE(pl.nama_pelanggan, a.nama_anggota, "Umum") as nama_pihak'),
                DB::raw('COALESCE(u.nama_lengkap, "-") as kasir'),
                'p.total_harga_penjualan as total',
                DB::raw('COALESCE(p.sumber_transaksi, "toko") as sumber')
            );

        $pembelianQuery = DB::table('pembelian as pb')
            ->join('supplier as s', 'pb.id_supplier', '=', 's.id_supplier')
            ->join('users as u', 'pb.id_user', '=', 'u.id_user')
            ->whereBetween('pb.tanggal_pembelian', [$start, $end])
            ->select(
                DB::raw('pb.tanggal_pembelian as tanggal'),
                'pb.id_pembelian as id',
                DB::raw("'pembelian' as jenis"),
                's.nama_supplier as nama_pihak',
                'u.nama_lengkap as kasir',
                'pb.jumlah_bayar as total',
                DB::raw("'toko' as sumber")
            );

        if ($jenis === 'jual') {
            $query = $penjualanQuery;
        } elseif ($jenis === 'beli') {
            $query = $pembelianQuery;
        } else {
            $query = $penjualanQuery->unionAll($pembelianQuery);
        }

    $transaksi = DB::query()
    ->fromSub($query, 't')
    ->orderBy('tanggal', $sortTanggal)
    ->paginate($perPage)
    ->appends($request->query());

        $transaksi = DB::query()
            ->fromSub($query, 't')
            ->orderBy('tanggal', $sortTanggal)
            ->paginate($perPage)
            ->appends($request->query());

        $transaksi->getCollection()->transform(fn($i) => tap($i, fn($i) => $i->tanggal = Carbon::parse($i->tanggal)));

        $daftarBarang = DB::table('barang')
            ->select('id_barang', 'nama_barang', 'harga_beli', 'margin', 'retail', 'stok')
            ->orderBy('nama_barang')
            ->get();

        return view('master.laporan.bulanan', compact(
            'periode', 'periodeTeks', 'omzet', 'hpp', 'labaKotor', 'marginPersen',
            'totalPembelian', 'pembelianMasuk', 'nilaiStokAkhir', 'stokKritis',
            'terlaris', 'transaksi', 'daftarBarang', 'jenis', 'sortTanggal', 'perPage'
        ));
    }
}