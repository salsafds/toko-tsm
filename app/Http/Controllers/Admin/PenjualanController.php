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
    /**
     * Index: list penjualan dengan search dan per_page
     */
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

    /**
     * Form create (dengan next ID dan dropdown)
     */
    public function create()
    {
        $nextId = $this->generateNextId();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $anggotas = Anggota::orderBy('nama_anggota')->get();
        $barangs = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();
        $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

        return view('admin.penjualan.create', compact('nextId', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang,id_barang',
            'barang.*.kuantitas' => 'required|integer|min:1',
            'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
            'ekspedisi' => 'nullable|boolean',
            'id_agen_ekspedisi' => 'required_if:ekspedisi,true|exists:agen_ekspedisi,id_ekspedisi',
            'nama_penerima' => 'required_if:ekspedisi,true|string|max:255',
            'telepon_penerima' => 'required_if:ekspedisi,true|string|max:20',
            'alamat_penerima' => 'required_if:ekspedisi,true|string',
            'kode_pos' => 'required_if:ekspedisi,true|string|max:10',
            'biaya_pengiriman' => 'required_if:ekspedisi,true|numeric|min:0',
        ], [
            'id_pelanggan.exists' => 'Pelanggan tidak valid.',
            'id_anggota.exists' => 'Anggota tidak valid.',
            'barang.required' => 'Minimal satu barang harus dipilih.',
            'barang.*.id_barang.required' => 'Barang wajib dipilih.',
            'barang.*.kuantitas.required' => 'Kuantitas wajib diisi.',
            'diskon_penjualan.max' => 'Diskon maksimal 100%.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
            'id_agen_ekspedisi.required_if' => 'Agen ekspedisi wajib dipilih jika ekspedisi dicentang.',
            'nama_penerima.required_if' => 'Nama penerima wajib diisi jika ekspedisi dicentang.',
            'telepon_penerima.required_if' => 'Telepon penerima wajib diisi jika ekspedisi dicentang.',
            'alamat_penerima.required_if' => 'Alamat penerima wajib diisi jika ekspedisi dicentang.',
            'kode_pos.required_if' => 'Kode pos wajib diisi jika ekspedisi dicentang.',
            'biaya_pengiriman.required_if' => 'Biaya pengiriman wajib diisi jika ekspedisi dicentang.',
        ]);

        // Validasi mutual exclusive pelanggan/anggota
        if (!$request->id_pelanggan && !$request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.']);
        }
        if ($request->id_pelanggan && $request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.']);
        }

        DB::transaction(function () use ($request) {
            $subTotal = 0;
            foreach ($request->barang as $item) {
                $barang = Barang::find($item['id_barang']);
                $subTotal += $barang->retail * $item['kuantitas'];
            }
            $diskon = $request->diskon_penjualan ?? 0;
            $totalHarga = $subTotal - ($subTotal * $diskon / 100);

            $penjualan = Penjualan::create([
                'id_penjualan' => $this->generateNextId(),
                'id_pelanggan' => $request->id_pelanggan,
                'id_anggota' => $request->id_anggota,
                'id_user' => Auth::user()->id_user,
                'diskon_penjualan' => $diskon,
                'total_harga_penjualan' => $totalHarga,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->barang as $item) {
                $barang = Barang::find($item['id_barang']);
                DetailPenjualan::create([
                    'id_detail_penjualan' => $this->generateDetailId(),
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'sub_total' => $barang->retail * $item['kuantitas'],
                ]);
                // Kurangi stok
                $barang->decrement('stok', $item['kuantitas']);
            }

            if ($request->ekspedisi) {
                Pengiriman::create([
                    'id_pengiriman' => $this->generatePengirimanId(),
                    'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                    'id_penjualan' => $penjualan->id_penjualan,
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

    /**
     * Form edit
     */
    public function edit($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.barang', 'pengiriman'])->findOrFail($id);
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        $anggotas = Anggota::orderBy('nama_anggota')->get();
        $barangs = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();
        $agenEkspedisis = AgenEkspedisi::orderBy('nama_ekspedisi')->get();

        return view('admin.penjualan.edit', compact('penjualan', 'pelanggans', 'anggotas', 'barangs', 'agenEkspedisis'));
    }

    /**
     * Update data penjualan
     */
    public function update(Request $request, $id)
    {
        // Validasi mirip store
        $request->validate([
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
            'barang' => 'required|array|min:1',
            'barang.*.id_barang' => 'required|exists:barang,id_barang',
            'barang.*.kuantitas' => 'required|integer|min:1',
            'diskon_penjualan' => 'nullable|numeric|min:0|max:100',
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:255',
            'ekspedisi' => 'nullable|boolean',
            'id_agen_ekspedisi' => 'required_if:ekspedisi,true|exists:agen_ekspedisi,id_ekspedisi',
            'nama_penerima' => 'required_if:ekspedisi,true|string|max:255',
            'telepon_penerima' => 'required_if:ekspedisi,true|string|max:20',
            'alamat_penerima' => 'required_if:ekspedisi,true|string',
            'kode_pos' => 'required_if:ekspedisi,true|string|max:10',
            'biaya_pengiriman' => 'required_if:ekspedisi,true|numeric|min:0',
        ]);

        if (!$request->id_pelanggan && !$request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Pilih pelanggan atau anggota.']);
        }
        if ($request->id_pelanggan && $request->id_anggota) {
            return back()->withErrors(['id_pelanggan' => 'Hanya boleh pilih satu: pelanggan atau anggota.']);
        }

        $penjualan = Penjualan::findOrFail($id);
        DB::transaction(function () use ($request, $penjualan) {
            // Restore stok lama
            foreach ($penjualan->detailPenjualan as $detail) {
                $detail->barang->increment('stok', $detail->kuantitas);
            }
            $penjualan->detailPenjualan()->delete();
            $penjualan->pengiriman()->delete();

            $subTotal = 0;
            foreach ($request->barang as $item) {
                $barang = Barang::find($item['id_barang']);
                $subTotal += $barang->retail * $item['kuantitas'];
            }
            $diskon = $request->diskon_penjualan ?? 0;
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
                $barang = Barang::find($item['id_barang']);
                DetailPenjualan::create([
                    'id_detail_penjualan' => $this->generateDetailId(),
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'sub_total' => $barang->retail * $item['kuantitas'],
                ]);
                $barang->decrement('stok', $item['kuantitas']);
            }

            if ($request->ekspedisi) {
                Pengiriman::create([
                    'id_pengiriman' => $this->generatePengirimanId(),
                    'id_agen_ekspedisi' => $request->id_agen_ekspedisi,
                    'id_penjualan' => $penjualan->id_penjualan,
                    'biaya_pengiriman' => $request->biaya_pengiriman,
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

    /**
     * Hapus data penjualan
     */
    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        // Restore stok
        foreach ($penjualan->detailPenjualan as $detail) {
            $detail->barang->increment('stok', $detail->kuantitas);
        }
        $penjualan->delete();

        return redirect()->route('admin.penjualan.index')
                         ->with('success', 'Data penjualan berhasil dihapus.');
    }

    /**
     * Mark as selesai
     */
    public function selesai($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['tanggal_selesai' => now()]);

        return redirect()->route('admin.penjualan.index')
                         ->with('success', 'Penjualan ditandai selesai.');
    }

    /**
     * Print struk
     */
    public function print($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.barang', 'pengiriman.agenEkspedisi', 'pelanggan', 'anggota', 'user'])->findOrFail($id);

        return view('admin.penjualan.print', compact('penjualan'));
    }

    /**
     * Generate next ID format PJ001, PJ002, ...
     */
    private function generateNextId()
    {
        $maxNum = Penjualan::selectRaw('MAX(CAST(SUBSTRING(id_penjualan, 3) AS UNSIGNED)) as max_num')
                          ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'PJ' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateDetailId()
    {
        $maxNum = DetailPenjualan::selectRaw('MAX(CAST(SUBSTRING(id_detail_penjualan, 3) AS UNSIGNED)) as max_num')
                                ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'DP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generatePengirimanId()
    {
        $maxNum = Pengiriman::selectRaw('MAX(CAST(SUBSTRING(id_pengiriman, 3) AS UNSIGNED)) as max_num')
                           ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'PG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
