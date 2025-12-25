<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
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
        if ($request->get('section') === 'daftar_barang') {
            
            $search = $request->get('search_barang');
            
            $query = DB::table('barang')->orderBy('nama_barang', 'asc');

            if ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('id_barang', 'like', "%{$search}%");
            }

            $daftarBarang = $query->get();
            $periodeTeks = 'Per Tanggal ' . date('d F Y');
            $format = $request->get('format', 'excel');

            if ($format === 'pdf') {
                $pdf = PDF::loadView('master.laporan.daftar-barang', compact('daftarBarang', 'periodeTeks'));
                return $pdf->download('Daftar_Barang_' . date('Ymd_Hi') . '.pdf');
            }

            return Excel::download(new class($daftarBarang) implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles {
                protected $data;
                public function __construct($data) { $this->data = $data; }
                
                public function collection() {
                    return $this->data->map(fn($item) => [
                        $item->id_barang,
                        $item->nama_barang,
                        $item->margin . '%',
                        $item->harga_beli,
                        $item->stok,
                        $item->retail
                    ]);
                }

                public function headings(): array {
                    return ['Kode Barang', 'Nama Barang', 'Margin', 'Harga Beli (Rp)', 'Stok', 'Harga Jual (Rp)'];
                }

                public function title(): string { return 'Daftar Barang'; }

                public function styles(Worksheet $sheet) {
                    $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                    $sheet->getStyle('D2:D' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('F2:F' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('#,##0');
                    return [];
                }
            }, 'Daftar_Barang_' . date('Ymd_Hi') . '.xlsx');
        }
        
        $periode = $request->get('periode', 'bulanan');

        switch ($periode) {
            case '7hari':
                $start = $request->filled('tanggal_awal') ? Carbon::parse($request->tanggal_awal)->startOfDay() : now()->subDays(6)->startOfDay();
                $end   = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir)->endOfDay() : now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '1bulan_terakhir':
                $start = $request->filled('tanggal_awal') ? Carbon::parse($request->tanggal_awal)->startOfDay() : now()->subDays(29)->startOfDay();
                $end   = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir)->endOfDay() : now()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case '3bulan':
                $start = $request->filled('tanggal_awal') ? Carbon::parse($request->tanggal_awal)->startOfDay() : now()->subMonths(2)->startOfMonth()->startOfDay();
                $end   = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir)->endOfDay() : now()->endOfMonth()->endOfDay();
                $periodeTeks = $start->format('j M') . ' – ' . $end->format('j M Y');
                break;
            case 'bulanan':
                $bulan = $request->filled('bulan') ? Carbon::createFromFormat('Y-m', $request->bulan) : now();
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
            ->where('stok', '>', 0)
            ->where('stok', '<', 10)
            ->get();

        $terlaris = DB::table('detail_penjualan as dp')
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
            ->limit(10)
            ->get();

        $penjualanData = DB::table('penjualan as p')
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
            )
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($i) => tap($i, fn($i) => $i->tanggal = Carbon::parse($i->tanggal)));

        $pembelianData = DB::table('pembelian as pb')
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
            )
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($i) => tap($i, fn($i) => $i->tanggal = Carbon::parse($i->tanggal)));

        $namaFile = "Laporan_Lengkap_" . str_replace([' ', '/'], '_', $periodeTeks);
        $format   = $request->get('format', 'excel');

        $dataView = compact(
            'periodeTeks', 'omzet', 'hpp', 'labaKotor', 'marginPersen',
            'totalPembelian', 'pembelianMasuk', 'nilaiStokAkhir',
            'stokKritis', 'terlaris'
        );
        
        $mergedTransaksi = $penjualanData->merge($pembelianData)->sortByDesc('tanggal');
        $dataView['transaksi'] = $mergedTransaksi;

        if ($format === 'pdf') {
            $pdf = PDF::loadView('master.laporan.bulanan-export', $dataView);
            return $pdf->download($namaFile . '.pdf');
        }

        return Excel::download(new class($penjualanData, $pembelianData, $dataView, $terlaris) implements WithMultipleSheets {
            protected $penjualan, $pembelian, $ringkasan, $terlaris;

            public function __construct($penjualan, $pembelian, $ringkasan, $terlaris) {
                $this->penjualan = $penjualan;
                $this->pembelian = $pembelian;
                $this->ringkasan = $ringkasan;
                $this->terlaris  = $terlaris;
            }

            public function sheets(): array {
                return [
                    new class($this->penjualan) implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles {
                        protected $data;
                        public function __construct($data) { $this->data = $data; }
                        public function collection() {
                            return $this->data->map(fn($r) => [
                                $r->tanggal->format('d/m/Y'), 
                                $r->id, 
                                $r->nama_pihak,
                                $r->kasir, 
                                $r->total, 
                                strtoupper($r->sumber)
                            ]);
                        }
                        public function headings(): array { return ['Tanggal', 'No Nota', 'Pembeli', 'Kasir', 'Total (Rp)', 'Sumber']; }
                        public function title(): string { return '1. Transaksi Penjualan'; }
                        public function styles(Worksheet $sheet) { return [1 => ['font' => ['bold' => true]]]; }
                    },

                    new class($this->pembelian) implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles {
                        protected $data;
                        public function __construct($data) { $this->data = $data; }
                        public function collection() {
                            return $this->data->map(fn($r) => [
                                $r->tanggal->format('d/m/Y'), 
                                $r->id, 
                                $r->nama_pihak,
                                $r->kasir, 
                                $r->total
                            ]);
                        }
                        public function headings(): array { return ['Tanggal', 'No Nota', 'Supplier', 'Kasir', 'Total (Rp)']; }
                        public function title(): string { return '2. Transaksi Pembelian'; }
                        public function styles(Worksheet $sheet) { return [1 => ['font' => ['bold' => true]]]; }
                    },

                    new class($this->ringkasan) implements FromCollection, WithTitle, ShouldAutoSize, WithStyles {
                        protected $d;
                        public function __construct($data) { $this->d = $data; }
                        public function collection() {
                            return collect([
                                ['LAPORAN RINGKASAN'],
                                ['Periode', $this->d['periodeTeks']],
                                ['Dicetak', date('d F Y H:i')],
                                [''],
                                ['1. KINERJA PENJUALAN'],
                                ['Omzet Penjualan', $this->d['omzet']],
                                ['HPP (Harga Pokok)', $this->d['hpp']],
                                ['Laba Kotor', $this->d['labaKotor']],
                                ['Margin Keuntungan', $this->d['marginPersen'] . '%'],
                                [''],
                                ['2. DATA PEMBELIAN'],
                                ['Total Pembelian', $this->d['totalPembelian']],
                                ['Barang Masuk Stok', $this->d['pembelianMasuk']],
                                [''],
                                ['3. DATA STOK'],
                                ['Nilai Stok Akhir', $this->d['nilaiStokAkhir']],
                                ['Jumlah Stok Kritis', $this->d['stokKritis']->count() . ' Item']
                            ]);
                        }
                        public function title(): string { return '3. Ringkasan Laporan'; }
                        public function styles(Worksheet $sheet) {
                            $sheet->getStyle('B6:B8')->getNumberFormat()->setFormatCode('#,##0');
                            $sheet->getStyle('B12:B13')->getNumberFormat()->setFormatCode('#,##0');
                            $sheet->getStyle('B16')->getNumberFormat()->setFormatCode('#,##0');
                            return [
                                1 => ['font' => ['bold' => true, 'size' => 14]],
                                5 => ['font' => ['bold' => true]],
                                11 => ['font' => ['bold' => true]],
                                15 => ['font' => ['bold' => true]],
                            ];
                        }
                    },

                    new class($this->terlaris) implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles, WithCharts {
                        protected $data;
                        public function __construct($data) { $this->data = $data; }
                        public function collection() {
                            return collect($this->data)->map(fn($item, $key) => [
                                $key + 1, $item->id_barang, $item->nama_barang, $item->qty, $item->omzet
                            ]);
                        }
                        public function headings(): array { return ['Rank', 'Kode Barang', 'Nama Barang', 'Terjual (Qty)', 'Omzet (Rp)']; }
                        public function title(): string { return '4. Barang Terlaris'; }
                        public function styles(Worksheet $sheet) { return [1 => ['font' => ['bold' => true]]]; }
                        public function charts() {
                            $count = count($this->data);
                            $title = $this->title();
                            $label = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'$title'!\$D\$1", null, 1)];
                            $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'$title'!\$C\$2:\$C$" . ($count + 1), null, $count)];
                            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'$title'!\$D\$2:\$D$" . ($count + 1), null, $count)];
                            $series = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_STANDARD, range(0, count($values) - 1), $label, $categories, $values);
                            $series->setPlotDirection(DataSeries::DIRECTION_COL);
                            $chart = new Chart('chart_terlaris', new Title('Grafik 10 Barang Terlaris (Qty)'), new Legend(Legend::POSITION_RIGHT, null, false), new PlotArea(null, [$series]), true, DataSeries::EMPTY_AS_GAP, null, null);
                            $chart->setTopLeftPosition('G2');
                            $chart->setBottomRightPosition('O20');
                            return [$chart];
                        }
                    },
                ];
            }
        }, $namaFile . '.xlsx');
    }}