<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Anggota;
use App\Models\Barang;
use App\Models\AgenEkspedisi;
use App\Models\Pengiriman;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'anggota', 'user']);

        if ($periode = $request->query('periode')) {
        switch ($periode) {
            case '7days':
                $query->where('tanggal_order', '>=', now()->subDays(7));
                break;
            case '3months':
                $query->where('tanggal_order', '>=', now()->subMonths(3));
                break;
            case '1year':
                $query->where('tanggal_order', '>=', now()->subYears(1));
                break;
            case 'all':
            default:
                break;
        }
    }
        
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_penjualan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($sub) use ($search) {
                      $sub->where('nama_pelanggan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('anggota', function ($sub) use ($search) {
                      $sub->where('nama_anggota', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $penjualans = $query->orderBy('tanggal_order', 'desc')->paginate($perPage);

        return view('admin.penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $anggotas = Anggota::orderBy('nama_anggota')->get();
        $barangs = Barang::orderBy('nama_barang')->get()->map(function($barang) {

        $qtyHold = DetailPenjualan::where('id_barang', $barang->id_barang)
                ->whereHas('penjualan', function($q) {
                    $q->whereNull('tanggal_selesai'); 
                })
                ->sum('kuantitas');

            $barang->stok_tersedia = max(0, $barang->stok - $qtyHold);
                
            return $barang;
        });
        $barangs = $barangs->where('stok_tersedia', '>', 0)->values();

        $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

        return view('admin.penjualan.create', compact('nextId', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
    }

    public function store(Request $request)
    {
        if (!$request->has('jenis_pembayaran') || empty($request->jenis_pembayaran)) {
            $request->merge(['jenis_pembayaran' => 'tunai']);
        }
        $rules = [
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang,id_barang',
            'barang.*.kuantitas' => 'required|integer|min:1',
            'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
            'tarif_ppn' => 'required|numeric|min:0|max:100',
            'jenis_pembayaran' => 'required|in:tunai',
            'uang_diterima' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
        ];

        if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
            $rules += [
                'id_agen_ekspedisi' => 'required|exists:agen_ekspedisi,id_ekspedisi',
                'nama_penerima' => 'required|string|max:255',
                'telepon_penerima' => 'required|string|max:20',
                'alamat_penerima' => 'required|string',
                'kode_pos' => 'required|string|max:10',
                'biaya_pengiriman' => 'nullable|numeric|min:0',
                'nomor_resi' => 'nullable|string|max:255',
            ];
        }

        // Hitung dulu apakah ada barang yang kena PPN
        $adaBarangKenaPPN = collect($request->barang)->contains(function ($item) {
            $barang = Barang::find($item['id_barang']);
            return $barang && (strtolower($barang->kena_ppn) === 'ya');
        });

        $rules['tarif_ppn'] = $adaBarangKenaPPN 
            ? 'required|numeric|min:0|max:100' 
            : 'nullable|numeric|min:0|max:100';

        $request->validate($rules);

        if (!$request->id_pelanggan && !$request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.'])->withInput();
        }
        if ($request->id_pelanggan && $request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            $subTotalBarang = 0;
        $total_dpp = 0;
        $total_non_ppn = 0;

        foreach ($request->barang as $item) {
            $barang = Barang::findOrFail($item['id_barang']);

            if ($barang->stok_tersedia < $item['kuantitas']) {
                throw new \Exception("Stok {$barang->nama_barang} tidak cukup! Tersedia: {$barang->stok_tersedia}, diminta: {$item['kuantitas']}");
            }

            $sub_total_item = $barang->retail * $item['kuantitas'];

            if ($barang->kena_ppn === 'ya' || strtolower($barang->kena_ppn) === 'ya') {
                $total_dpp += $sub_total_item;
            } else {
                $total_non_ppn += $sub_total_item;
            }
        }

        $biayaPengiriman = $request->has('ekspedisi') && $request->ekspedisi == '1' ? ($request->biaya_pengiriman ?? 0) : 0;

        $subTotalBarangDanOngkir = $total_dpp + $total_non_ppn + $biayaPengiriman;

        $diskonPersen = $request->filled('diskon_penjualan') ? $request->diskon_penjualan : 0;
        $diskonNilai = $subTotalBarangDanOngkir * ($diskonPersen / 100);

        $dppSetelahDiskon = $total_dpp - ($total_dpp * $diskonPersen / 100);

        $tarif_ppn = $request->tarif_ppn;
        $total_ppn = round($dppSetelahDiskon * $tarif_ppn / 100);

        $totalHarga = $subTotalBarangDanOngkir - $diskonNilai + $total_ppn;

                    $penjualan = Penjualan::create([
                        'id_penjualan' => $this->generateNextId(),
                        'id_pelanggan' => $request->id_pelanggan,
                        'id_anggota' => $request->id_anggota,
                        'id_user' => Auth::id(),
                        'sumber_transaksi' => 'toko',
                        'diskon_penjualan' => $diskonPersen,
                        'tarif_ppn' => $tarif_ppn,
                        'total_dpp' => round($dppSetelahDiskon),
                        'total_ppn' => $total_ppn,
                        'total_non_ppn' => $total_non_ppn + $biayaPengiriman,
                        'total_harga_penjualan' => $totalHarga,
                        'jenis_pembayaran' => $request->jenis_pembayaran,
                        'uang_diterima' => $request->uang_diterima ?? 0,
                        'catatan' => $request->catatan,
                    ]);
                    foreach ($request->barang as $item) {
                        $barang = Barang::findOrFail($item['id_barang']);
                        DetailPenjualan::create([
                            'id_detail_penjualan' => $this->generateDetailId(),
                            'id_penjualan' => $penjualan->id_penjualan,
                            'id_barang' => $item['id_barang'],
                            'kuantitas' => $item['kuantitas'],
                            'sub_total' => $barang->retail * $item['kuantitas'],
                        ]);
                    }

                    if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
                        Pengiriman::create([
                            'id_pengiriman' => $this->generatePengirimanId(),
                            'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                            'id_penjualan' => $penjualan->id_penjualan,
                            'nomor_resi' => $request->nomor_resi,
                            'biaya_pengiriman' => $biayaPengiriman,
                            'nama_penerima' => $request->nama_penerima,
                            'telepon_penerima' => $request->telepon_penerima,
                            'alamat_penerima' => $request->alamat_penerima,
                            'kode_pos' => $request->kode_pos,
                        ]);
                    }
                });

                return redirect()->route('admin.penjualan.index')
                                ->with('success', 'Data penjualan berhasil ditambahkan.');
            }

            public function edit($id)
            {
                $penjualan = Penjualan::with(['detailPenjualan.barang', 'pengiriman'])->findOrFail($id);
                
                if ($penjualan->tanggal_selesai) {
                    return back()->withErrors(['error' => 'Transaksi sudah selesai dan tidak bisa diedit.']);
                }

                $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
                $anggotas = Anggota::orderBy('nama_anggota')->get();
                $barangs = Barang::orderBy('nama_barang')->get()->map(function($barang) {

                $qtyHold = DetailPenjualan::where('id_barang', $barang->id_barang)
                        ->whereHas('penjualan', function($q) {
                            $q->whereNull('tanggal_selesai'); 
                        })
                        ->sum('kuantitas');

                    $barang->stok_tersedia = max(0, $barang->stok - $qtyHold);
                        
                    return $barang;
                });
                $barangs = $barangs->where('stok_tersedia', '>', 0)->values();

                $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

                return view('admin.penjualan.edit', compact('penjualan', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
            }

            public function update(Request $request, $id)
        {
            $penjualan = Penjualan::findOrFail($id);

            if ($penjualan->tanggal_selesai) {
                return back()->withErrors(['error' => 'Transaksi sudah selesai dan tidak bisa diubah.']);
            }
                if (!$request->has('jenis_pembayaran') || empty($request->jenis_pembayaran)) {
                    $request->merge(['jenis_pembayaran' => 'tunai']);
                }

                $rules = [
                    'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
                    'id_anggota' => 'nullable|exists:anggota,id_anggota',
                    'barang' => 'required|array|min:1',
                    'barang.*.id_barang' => 'required|exists:barang,id_barang',
                    'barang.*.kuantitas' => 'required|integer|min:1',
                    'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
                    'tarif_ppn' => 'required|numeric|min:0|max:100',
                    'jenis_pembayaran' => 'required|in:tunai',
                    'uang_diterima' => 'nullable|numeric|min:0',
                    'catatan' => 'nullable|string|max:255',
                ];

                if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
                    $rules += [
                        'id_agen_ekspedisi' => 'required|exists:agen_ekspedisi,id_ekspedisi',
                        'nama_penerima' => 'required|string|max:255',
                        'telepon_penerima' => 'required|string|max:20',
                        'alamat_penerima' => 'required|string',
                        'kode_pos' => 'required|string|max:10',
                        'biaya_pengiriman' => 'nullable|numeric|min:0',
                        'nomor_resi' => 'nullable|string|max:255',
                    ];
                }

                // Cek apakah ada barang yang kena PPN
                $adaKenaPPN = false;
                if ($request->has('barang') && is_array($request->barang)) {
                    foreach ($request->barang as $item) {
                        $barang = Barang::find($item['id_barang']);
                        if ($barang && strtolower($barang->kena_ppn) === 'ya') {
                            $adaKenaPPN = true;
                            break;
                        }
                    }
                }

                // Atur rule tarif_ppn secara dinamis
                $rules['tarif_ppn'] = $adaKenaPPN
                    ? 'required|numeric|min:0|max:100'
                    : 'nullable|numeric|min:0|max:100';

                $request->validate($rules);

                if (!$request->id_pelanggan && !$request->id_anggota) {
                    return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.'])->withInput();
                }
                if ($request->id_pelanggan && $request->id_anggota) {
                    return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.'])->withInput();
                }

                DB::transaction(function () use ($request, $penjualan) {

                $penjualan->detailPenjualan()->delete();
                $penjualan->pengiriman()->delete();

                $total_dpp = 0;
                $total_non_ppn = 0;

                foreach ($request->barang as $item) {
                    $barang = Barang::findOrFail($item['id_barang']);

                    if ($barang->stok_tersedia < $item['kuantitas']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak cukup!");
                    }

                    $sub_total_item = $barang->retail * $item['kuantitas'];

                    if ($barang->kena_ppn === 'ya' || strtolower($barang->kena_ppn) === 'ya') {
                        $total_dpp += $sub_total_item;
                    } else {
                        $total_non_ppn += $sub_total_item;
                    }
                }

                $biayaPengiriman = $request->has('ekspedisi') && $request->ekspedisi == '1' 
                    ? ($request->biaya_pengiriman ?? 0) 
                    : 0;

                $subTotalBarangDanOngkir = $total_dpp + $total_non_ppn + $biayaPengiriman;

                $diskonPersen = $request->filled('diskon_penjualan') ? $request->diskon_penjualan : 0;
                $diskonNilai = $subTotalBarangDanOngkir * ($diskonPersen / 100);

                $dppSetelahDiskon = $total_dpp - ($total_dpp * $diskonPersen / 100);

                $tarif_ppn = $request->tarif_ppn;
                $total_ppn = round($dppSetelahDiskon * $tarif_ppn / 100);

                $totalHarga = $subTotalBarangDanOngkir - $diskonNilai + $total_ppn;

                $penjualan->update([
                    'id_pelanggan' => $request->id_pelanggan,
                    'id_anggota' => $request->id_anggota,
                    'id_user' => Auth::id(),
                    'sumber_transaksi' => 'toko',
                    'diskon_penjualan' => $diskonPersen,
                    'tarif_ppn' => $tarif_ppn,
                    'total_dpp' => round($dppSetelahDiskon),
                    'total_ppn' => $total_ppn,
                    'total_non_ppn' => $total_non_ppn + $biayaPengiriman,
                    'total_harga_penjualan' => $totalHarga,
                    'jenis_pembayaran' => $request->jenis_pembayaran,
                    'uang_diterima' => $request->uang_diterima ?? 0,
                    'catatan' => $request->catatan,
                ]);

                foreach ($request->barang as $item) {
                    $barang = Barang::findOrFail($item['id_barang']);
                    DetailPenjualan::create([
                        'id_detail_penjualan' => $this->generateDetailId(),
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_barang' => $item['id_barang'],
                        'kuantitas' => $item['kuantitas'],
                        'sub_total' => $barang->retail * $item['kuantitas'],
                    ]);
                }

                if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
                    Pengiriman::create([
                        'id_pengiriman' => $this->generatePengirimanId(),
                        'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                        'id_penjualan' => $penjualan->id_penjualan,
                        'nomor_resi' => $request->nomor_resi,
                        'biaya_pengiriman' => $biayaPengiriman,
                        'nama_penerima' => $request->nama_penerima,
                        'telepon_penerima' => $request->telepon_penerima,
                        'alamat_penerima' => $request->alamat_penerima,
                        'kode_pos' => $request->kode_pos,
                    ]);
                }
            });

            return redirect()->route('admin.penjualan.index')
                            ->with('success', 'Data penjualan berhasil diperbarui.');
        }
            public function destroy($id)
            {
                $penjualan = Penjualan::findOrFail($id);
                if ($penjualan->tanggal_selesai) {
                    return back()->withErrors(['error' => 'Transaksi sudah selesai dan tidak bisa dihapus.']);
                }

                $penjualan->detailPenjualan()->delete();
                $penjualan->pengiriman()->delete();
                $penjualan->delete();

                return redirect()->route('admin.penjualan.index')
                                ->with('success', 'Data penjualan berhasil dihapus.');
            }

            public function selesai($id)
        {
            $penjualan = Penjualan::with('detailPenjualan.barang')->findOrFail($id);

            if ($penjualan->tanggal_selesai) {
                return redirect()->route('admin.penjualan.index')
                    ->with('info', 'Transaksi sudah selesai sebelumnya.');
            }

            DB::transaction(function () use ($penjualan) {
                foreach ($penjualan->detailPenjualan as $detail) {
                    app(BarangController::class)->kurangiStokDariPenjualan(
                        $detail->id_barang,
                        $detail->kuantitas
                    );
                }

                $penjualan->update(['tanggal_selesai' => now()]);
            });

            return redirect()->route('admin.penjualan.index')
                ->with('success', 'Penjualan selesai! Stok telah dikurangi.');
        }
            public function print($id)
            {
                $penjualan = Penjualan::with(['detailPenjualan.barang', 'pengiriman.agenEkspedisi', 'pelanggan', 'anggota', 'user'])->findOrFail($id);
                return view('admin.penjualan.print', compact('penjualan'));
            }

            private function generateNextId()
            {
                $maxNum = Penjualan::max(DB::raw('CAST(SUBSTRING(id_penjualan, 3) AS UNSIGNED)')) ?? 0;
                return 'PJ' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            }

            private function generateDetailId()
            {
                $maxNum = DetailPenjualan::max(DB::raw('CAST(SUBSTRING(id_detail_penjualan, 3) AS UNSIGNED)')) ?? 0;
                return 'DP' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            }

            private function generatePengirimanId()
            {
                $maxNum = Pengiriman::max(DB::raw('CAST(SUBSTRING(id_pengiriman, 3) AS UNSIGNED)')) ?? 0;
                return 'PG' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            }
        }