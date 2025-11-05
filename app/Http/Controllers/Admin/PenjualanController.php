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

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_penjualan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($sub) use ($search) {
                      $sub->where('nama_pelanggan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('anggota', function ($sub) use ($search) {
                      $sub->where('nama_anggota', 'like', "%{$search}%");
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
        $barangs = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();
        $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

        return view('admin.penjualan.create', compact('nextId', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
    }

    public function store(Request $request)
    {
        $rules = [
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang,id_barang',
            'barang.*.kuantitas' => 'required|integer|min:1',
            'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
        ];

        $messages = [
            'id_pelanggan.exists' => 'Pelanggan tidak valid.',
            'id_anggota.exists' => 'Anggota tidak valid.',
            'barang.required' => 'Minimal satu barang harus dipilih.',
            'barang.*.id_barang.required' => 'Barang wajib dipilih.',
            'barang.*.kuantitas.required' => 'Kuantitas wajib diisi.',
            'diskon_penjualan.max' => 'Diskon maksimal 100%.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
        ];

        // Hanya validasi ekspedisi jika checkbox dikirim dan bernilai 1
        if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
            $rules += [
                'id_agen_ekspedisi' => 'required|exists:agen_ekspedisi,id_ekspedisi',
                'nama_penerima' => 'required|string|max:255',
                'telepon_penerima' => 'required|string|max:20',
                'alamat_penerima' => 'required|string',
                'kode_pos' => 'required|string|max:10',
                'biaya_pengiriman' => 'required|numeric|min:0',
                'nomor_resi' => 'nullable|string|max:255',
            ];

            $messages += [
                'id_agen_ekspedisi.required' => 'Agen ekspedisi wajib dipilih.',
                'id_agen_ekspedisi.exists' => 'Agen ekspedisi tidak valid.',
                'nama_penerima.required' => 'Nama penerima wajib diisi.',
                'telepon_penerima.required' => 'Telepon penerima wajib diisi.',
                'alamat_penerima.required' => 'Alamat penerima wajib diisi.',
                'kode_pos.required' => 'Kode pos wajib diisi.',
                'biaya_pengiriman.required' => 'Biaya pengiriman wajib diisi.',
            ];
        }

        $request->validate($rules, $messages);

        // Validasi mutual exclusive
        if (!$request->id_pelanggan && !$request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.'])->withInput();
        }
        if ($request->id_pelanggan && $request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            $subTotal = 0;
            foreach ($request->barang as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subTotal += $barang->retail * $item['kuantitas'];
            }
            $diskon = $request->filled('diskon_penjualan') ? $request->diskon_penjualan : 0;
            $totalHarga = $subTotal - ($subTotal * $diskon / 100);

            $penjualan = Penjualan::create([
                'id_penjualan' => $this->generateNextId(),
                'id_pelanggan' => $request->id_pelanggan,
                'id_anggota' => $request->id_anggota,
                'id_user' => Auth::id(),
                'diskon_penjualan' => $diskon,
                'total_harga_penjualan' => $totalHarga,
                'jenis_pembayaran' => $request->jenis_pembayaran,
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
                $barang->decrement('stok', $item['kuantitas']);
            }

            if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
                Pengiriman::create([
                    'id_pengiriman' => $this->generatePengirimanId(),
                    'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                    'id_penjualan' => $penjualan->id_penjualan,
                    'nomor_resi' => $request->nomor_resi,
                    'biaya_pengiriman' => $request->biaya_pengiriman,
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
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $anggotas = Anggota::orderBy('nama_anggota')->get();
        $barangs = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();
        $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

        return view('admin.penjualan.edit', compact('penjualan', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang,id_barang',
            'barang.*.kuantitas' => 'required|integer|min:1',
            'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
        ];

        $messages = [
            'id_pelanggan.exists' => 'Pelanggan tidak valid.',
            'id_anggota.exists' => 'Anggota tidak valid.',
            'barang.required' => 'Minimal satu barang harus dipilih.',
            'barang.*.id_barang.required' => 'Barang wajib dipilih.',
            'barang.*.kuantitas.required' => 'Kuantitas wajib diisi.',
            'diskon_penjualan.max' => 'Diskon maksimal 100%.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
        ];

        if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
            $rules += [
                'id_agen_ekspedisi' => 'required|exists:agen_ekspedisi,id_ekspedisi',
                'nama_penerima' => 'required|string|max:255',
                'telepon_penerima' => 'required|string|max:20',
                'alamat_penerima' => 'required|string',
                'kode_pos' => 'required|string|max:10',
                'biaya_pengiriman' => 'required|numeric|min:0',
                'nomor_resi' => 'nullable|string|max:255',
            ];

            $messages += [
                'id_agen_ekspedisi.required' => 'Agen ekspedisi wajib dipilih.',
                'id_agen_ekspedisi.exists' => 'Agen ekspedisi tidak valid.',
                'nama_penerima.required' => 'Nama penerima wajib diisi.',
                'telepon_penerima.required' => 'Telepon penerima wajib diisi.',
                'alamat_penerima.required' => 'Alamat penerima wajib diisi.',
                'kode_pos.required' => 'Kode pos wajib diisi.',
                'biaya_pengiriman.required' => 'Biaya pengiriman wajib diisi.',
            ];
        }

        $request->validate($rules, $messages);

        if (!$request->id_pelanggan && !$request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.'])->withInput();
        }
        if ($request->id_pelanggan && $request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.'])->withInput();
        }

        $penjualan = Penjualan::findOrFail($id);

        DB::transaction(function () use ($request, $penjualan) {
            foreach ($penjualan->detailPenjualan as $detail) {
                $detail->barang->increment('stok', $detail->kuantitas);
            }
            $penjualan->detailPenjualan()->delete();
            $penjualan->pengiriman()->delete();

            $subTotal = 0;
            foreach ($request->barang as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subTotal += $barang->retail * $item['kuantitas'];
            }
            $diskon = $request->filled('diskon_penjualan') ? $request->diskon_penjualan : 0;
            $totalHarga = $subTotal - ($subTotal * $diskon / 100);

            $penjualan->update([
                'id_pelanggan' => $request->id_pelanggan,
                'id_anggota' => $request->id_anggota,
                'diskon_penjualan' => $diskon,
                'total_harga_penjualan' => $totalHarga,
                'jenis_pembayaran' => $request->jenis_pembayaran,
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
                $barang->decrement('stok', $item['kuantitas']);
            }

            if ($request->has('ekspedisi') && $request->ekspedisi == '1') {
                Pengiriman::create([
                    'id_pengiriman' => $this->generatePengirimanId(),
                    'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                    'id_penjualan' => $penjualan->id_penjualan,
                    'biaya_pengiriman' => $request->biaya_pengiriman,
                    'nama_penerima' => $request->nama_penerima,
                    'telepon_penerima' => $request->telepon_penerima,
                    'alamat_penerima' => $request->alamat_penerima,
                    'kode_pos' => $request->kode_pos,
                    'nomor_resi' => $request->nomor_resi,
                ]);
            }
        });

        return redirect()->route('admin.penjualan.index')
                         ->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        foreach ($penjualan->detailPenjualan as $detail) {
            $detail->barang->increment('stok', $detail->kuantitas);
        }
        $penjualan->delete();

        return redirect()->route('admin.penjualan.index')
                         ->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function selesai($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['tanggal_selesai' => now()]);

        return redirect()->route('admin.penjualan.index')
                         ->with('success', 'Penjualan ditandai selesai.');
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