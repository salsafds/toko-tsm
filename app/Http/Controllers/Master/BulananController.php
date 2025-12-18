<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BulananController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');

        switch ($periode) {
            case '7hari':
                $start = now()->subDays(6)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j') . ' – ' . $end->format('j F Y');
                break;
            case '1bulan_terakhir':
                $start = now()->subDays(29)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '3bulan':
                $start = now()->subMonths(2)->startOfMonth();
                $end   = now()->endOfMonth();
                $periodeTeks = $start->translatedFormat('F') . ' – ' . $end->translatedFormat('F Y');
                break;
            case 'bulanan':
                $bulan = $request->filled('bulan')
                    ? Carbon::createFromFormat('Y-m', $request->bulan)
                    : now();
                $start = $bulan->copy()->startOfMonth()->startOfDay();
                $end   = $bulan->copy()->endOfMonth()->endOfDay();
                $periodeTeks = $bulan->translatedFormat('F Y');
                break;
            case 'tahunan':
                $tahun = (int) $request->get('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end   = Carbon::create($tahun)->endOfYear();
                $periodeTeks = $tahun;
                break;
            default:
                $periode = 'bulanan';
                $start = now()->startOfMonth()->startOfDay();
                $end   = now()->endOfMonth()->endOfDay();
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

        $labaKotor    = $omzet - $hpp;
        $marginPersen = $omzet > 0 ? round(($labaKotor / $omzet) * 100, 2) : 0;

        $totalPembelian = DB::table('pembelian')
            ->whereBetween('tanggal_pembelian', [$start, $end])
            ->sum('jumlah_bayar');

        $pembelianMasuk = DB::table('pembelian')
            ->whereNotNull('tanggal_terima')
            ->whereBetween('tanggal_terima', [$start, $end])
            ->sum('jumlah_bayar');

        $nilaiStokAkhir = DB::table('barang')->sum(DB::raw('stok * harga_beli'));

        // Stok Kritis
        $sortStok    = $request->get('sort_stok', 'asc');
        $perPageStok = (int) $request->get('per_page_stok', 10);

        $stokKritis = DB::table('barang')
            ->where('stok', '>', 0)
            ->where('stok', '<', 10)
            ->orderBy('stok', $sortStok)
            ->select('id_barang', 'nama_barang', 'stok')
            ->paginate($perPageStok, ['*'], 'page_stok')
            ->appends($request->query());

        // 10 Barang Terlaris
        $terlaris = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select('b.id_barang', 'b.nama_barang', DB::raw('SUM(dp.kuantitas) as qty'), DB::raw('SUM(dp.sub_total) as omzet'))
            ->groupBy('b.id_barang', 'b.nama_barang')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // Riwayat Transaksi
        $jenis       = $request->get('jenis', 'all');
        $sortTanggal = $request->get('sort_tanggal', 'desc');
        $perPage     = (int) $request->get('per_page', 15);

        $penjualanQuery = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->leftJoin('anggota as a', 'p.id_anggota', '=', 'a.id_anggota')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id_user')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select(
                DB::raw('p.tanggal_selesai as tanggal'), 'p.id_penjualan as id',
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
                DB::raw('pb.tanggal_pembelian as tanggal'), 'pb.id_pembelian as id',
                DB::raw("'pembelian' as jenis"), 's.nama_supplier as nama_pihak',
                'u.nama_lengkap as kasir', 'pb.jumlah_bayar as total',
                DB::raw("'toko' as sumber")
            );

        $query = $jenis === 'jual' ? $penjualanQuery
               : ($jenis === 'beli' ? $pembelianQuery : $penjualanQuery->unionAll($pembelianQuery));

        $transaksi = DB::query()
            ->fromSub($query, 't')
            ->orderBy('tanggal', $sortTanggal)
            ->paginate($perPage)
            ->appends($request->query());

        $transaksi->getCollection()->transform(fn($i) => tap($i, fn($i) => $i->tanggal = Carbon::parse($i->tanggal)));

        // Daftar Barang
        $perPageBarang = (int) $request->get('per_page_barang', 25);
        $daftarBarang = DB::table('barang')
            ->select('id_barang', 'nama_barang', 'harga_beli', 'margin', 'retail', 'stok')
            ->orderBy('nama_barang')
            ->paginate($perPageBarang, ['*'], 'page_barang')
            ->appends($request->query());

        return view('master.laporan.bulanan', compact(
            'periode', 'periodeTeks', 'omzet', 'hpp', 'labaKotor', 'marginPersen',
            'totalPembelian', 'pembelianMasuk', 'nilaiStokAkhir', 'stokKritis',
            'terlaris', 'transaksi', 'daftarBarang', 'jenis', 'sortTanggal',
            'perPage', 'sortStok', 'perPageStok', 'perPageBarang'
        ));
    }

    // ====================== BARU: Halaman lengkap barang terlaris ======================
    public function terlaris(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');

        // Logika periode sama persis seperti di index()
        switch ($periode) {
            case '7hari':
                $start = now()->subDays(6)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j') . ' – ' . $end->format('j F Y');
                break;
            case '1bulan_terakhir':
                $start = now()->subDays(29)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '3bulan':
                $start = now()->subMonths(2)->startOfMonth();
                $end   = now()->endOfMonth();
                $periodeTeks = $start->translatedFormat('F') . ' – ' . $end->translatedFormat('F Y');
                break;
            case 'bulanan':
                $bulan = $request->filled('bulan')
                    ? Carbon::createFromFormat('Y-m', $request->bulan)
                    : now();
                $start = $bulan->copy()->startOfMonth()->startOfDay();
                $end   = $bulan->copy()->endOfMonth()->endOfDay();
                $periodeTeks = $bulan->translatedFormat('F Y');
                break;
            case 'tahunan':
                $tahun = (int) $request->get('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end   = Carbon::create($tahun)->endOfYear();
                $periodeTeks = $tahun;
                break;
            default:
                $periode = 'bulanan';
                $start = now()->startOfMonth()->startOfDay();
                $end   = now()->endOfMonth()->endOfDay();
                $periodeTeks = now()->translatedFormat('F Y');
        }

        $perPage = (int) $request->get('per_page', 25);

        $terlarisAll = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select(
                'b.id_barang',
                'b.nama_barang',
                DB::raw('SUM(dp.kuantitas) as qty'),
                DB::raw('SUM(dp.sub_total) as omzet')
            )
            ->groupBy('b.id_barang', 'b.nama_barang')
            ->orderByDesc('qty')
            ->paginate($perPage)
            ->appends($request->query());

        return view('master.laporan.terlaris', compact('terlarisAll', 'periodeTeks', 'periode'));
    }

    // ====================== BARU: Export barang terlaris (PDF & Excel) ======================
    public function exportTerlaris(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');

        switch ($periode) {
            case '7hari':
                $start = now()->subDays(6)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j') . ' – ' . $end->format('j F Y');
                break;
            case '1bulan_terakhir':
                $start = now()->subDays(29)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '3bulan':
                $start = now()->subMonths(2)->startOfMonth();
                $end   = now()->endOfMonth();
                $periodeTeks = $start->translatedFormat('F') . ' – ' . $end->translatedFormat('F Y');
                break;
            case 'bulanan':
                $bulan = $request->filled('bulan')
                    ? Carbon::createFromFormat('Y-m', $request->bulan)
                    : now();
                $start = $bulan->copy()->startOfMonth()->startOfDay();
                $end   = $bulan->copy()->endOfMonth()->endOfDay();
                $periodeTeks = $bulan->translatedFormat('F Y');
                break;
            case 'tahunan':
                $tahun = (int) $request->get('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end   = Carbon::create($tahun)->endOfYear();
                $periodeTeks = $tahun;
                break;
            default:
                $start = now()->startOfMonth()->startOfDay();
                $end   = now()->endOfMonth()->endOfDay();
                $periodeTeks = now()->translatedFormat('F Y');
        }

        $terlarisAll = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select(
                'b.id_barang',
                'b.nama_barang',
                DB::raw('SUM(dp.kuantitas) as qty'),
                DB::raw('SUM(dp.sub_total) as omzet')
            )
            ->groupBy('b.id_barang', 'b.nama_barang')
            ->orderByDesc('qty')
            ->get();

        $format = $request->get('format', 'pdf');
        $namaFile = "Barang_Terlaris_" . str_replace([' ', '/'], '_', $periodeTeks);

        if ($format === 'excel') {
            return Excel::download(new class($terlarisAll, $periodeTeks) implements \Maatwebsite\Excel\Concerns\FromCollection {
                protected $data;
                protected $periode;

                public function __construct($data, $periode)
                {
                    $this->data = $data;
                    $this->periode = $periode;
                }

                public function collection()
                {
                    $rows = collect();
                    $rows->push(['Rank', 'Kode Barang', 'Nama Barang', 'Terjual (Qty)', 'Omzet (Rp)']);

                    foreach ($this->data as $i => $item) {
                        $rows->push([
                            $i + 1,
                            $item->id_barang,
                            $item->nama_barang,
                            $item->qty,
                            number_format($item->omzet, 0, ',', '.')
                        ]);
                    }
                    return $rows;
                }

                public function title(): string
                {
                    return 'Barang Terlaris ' . $this->periode;
                }
            }, $namaFile . '.xlsx');
        }

        // PDF
        $pdf = PDF::loadView('master.laporan.terlaris-export', compact('terlarisAll', 'periodeTeks'));
        return $pdf->download($namaFile . '.pdf');
    }

    public function export(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');

        switch ($periode) {
            case '7hari':
                $start = now()->subDays(6)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j') . ' – ' . $end->format('j F Y');
                break;
            case '1bulan_terakhir':
                $start = now()->subDays(29)->startOfDay();
                $end   = now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '3bulan':
                $start = now()->subMonths(2)->startOfMonth();
                $end   = now()->endOfMonth();
                $periodeTeks = $start->translatedFormat('F') . ' – ' . $end->translatedFormat('F Y');
                break;
            case 'bulanan':
                $bulan = $request->filled('bulan')
                    ? Carbon::createFromFormat('Y-m', $request->bulan)
                    : now();
                $start = $bulan->copy()->startOfMonth()->startOfDay();
                $end   = $bulan->copy()->endOfMonth()->endOfDay();
                $periodeTeks = $bulan->translatedFormat('F Y');
                break;
            case 'tahunan':
                $tahun = (int) $request->get('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end   = Carbon::create($tahun)->endOfYear();
                $periodeTeks = $tahun;
                break;
            default:
                $start = now()->startOfMonth()->startOfDay();
                $end   = now()->endOfMonth()->endOfDay();
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

        $labaKotor    = $omzet - $hpp;
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
            ->where('stok', '>', 0)->where('stok', '<', 10)
            ->orderBy('stok')->get(['id_barang', 'nama_barang', 'stok']);

        $terlaris = DB::table('detail_penjualan as dp')
            ->join('penjualan as p', 'dp.id_penjualan', '=', 'p.id_penjualan')
            ->join('barang as b', 'dp.id_barang', '=', 'b.id_barang')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select('b.nama_barang', DB::raw('SUM(dp.kuantitas) as qty'), DB::raw('SUM(dp.sub_total) as omzet'))
            ->groupBy('b.id_barang', 'b.nama_barang')
            ->orderByDesc('qty')->limit(10)->get();

        $jenis = $request->get('jenis', 'all');

        $penjualanQuery = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->leftJoin('anggota as a', 'p.id_anggota', '=', 'a.id_anggota')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id_user')
            ->whereBetween('p.tanggal_selesai', [$start, $end])
            ->select(
                DB::raw('p.tanggal_selesai as tanggal'), 'p.id_penjualan as id',
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
                DB::raw('pb.tanggal_pembelian as tanggal'), 'pb.id_pembelian as id',
                DB::raw("'pembelian' as jenis"), 's.nama_supplier as nama_pihak',
                'u.nama_lengkap as kasir', 'pb.jumlah_bayar as total',
                DB::raw("'toko' as sumber")
            );

        $finalQuery = $jenis === 'jual' ? $penjualanQuery
                    : ($jenis === 'beli' ? $pembelianQuery : $penjualanQuery->unionAll($pembelianQuery));

        $transaksi = DB::table(DB::raw("({$finalQuery->toSql()}) as t"))
            ->mergeBindings($finalQuery)
            ->orderBy('tanggal', $request->get('sort_tanggal', 'desc'))
            ->get()
            ->map(fn($i) => tap($i, fn($i) => $i->tanggal = Carbon::parse($i->tanggal)));

        $format  = $request->get('format', 'pdf');  
        $section = $request->get('section');

        if ($section === 'daftar_barang') {
            $daftarBarang = DB::table('barang')
                ->select('id_barang','nama_barang','harga_beli','margin','retail','stok')
                ->orderBy('nama_barang')->get();

            $namaFile = "Daftar_Barang_{$periodeTeks}";

            if ($format === 'excel') {
                return Excel::download(new class($daftarBarang, $periodeTeks) implements \Maatwebsite\Excel\Concerns\FromView {
                    protected $data, $periode;
                    public function __construct($d, $p) { $this->data = $d; $this->periode = $p; }
                    public function view(): \Illuminate\Contracts\View\View {
                        return view('master.laporan.daftar-barang', [
                            'daftarBarang' => $this->data,
                            'periodeTeks'  => $this->periode
                        ]);
                    }
                }, $namaFile . '.xlsx');
            }

            $pdf = PDF::loadView('master.laporan.daftar-barang', compact('daftarBarang', 'periodeTeks'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download($namaFile . '.pdf');
        }
 
        $namaFile = "Laporan_Bulanan_" . str_replace([' ', '/'], '_', $periodeTeks);
        $dataExport = compact('periodeTeks','omzet','hpp','labaKotor','marginPersen','totalPembelian',
                              'pembelianMasuk','nilaiStokAkhir','stokKritis','terlaris','transaksi');

        if ($format === 'excel') {
            return Excel::download(new class($dataExport) implements \Maatwebsite\Excel\Concerns\FromCollection {
                protected $data;
                public function __construct($data) { $this->data = $data; }

                public function collection()
                {
                    $rows = collect();
                    $rows->push(['TANGGAL', 'ID', 'JENIS', 'PIHAK', 'KASIR', 'TOTAL (Rp)', 'SUMBER']);

                    foreach ($this->data['transaksi'] as $t) {
                        $rows->push([
                            $t->tanggal,
                            $t->id,
                            $t->jenis === 'penjualan' ? 'PENJUALAN' : 'PEMBELIAN',
                            $t->nama_pihak,
                            $t->kasir,
                            number_format($t->total, 0, ',', '.'),
                            strtoupper($t->sumber)
                        ]);
                    }
                    return $rows;
                }
            }, $namaFile . '.xlsx');
        }

        // PDF tetap cantik
        $pdf = PDF::loadView('master.laporan.bulanan-export', $dataExport);
        return $pdf->download($namaFile . '.pdf');
    }
}