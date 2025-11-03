<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Barang;
use App\Models\KategoriBarang; 
use App\Models\Satuan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with(['supplier', 'user', 'detailPembelian'])
            ->where('id_user', Auth::user()->id_user)
            ->orderBy('tanggal_pembelian', 'desc');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_pembelian', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('nama_supplier', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->query('per_page', 10);
        $pembelian = $query->paginate($perPage);

        return view('admin.pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all(); 
        $satuan = Satuan::all();
        $nextId = $this->generateNextId();
        $nextIdBarang = $this->generateNextIdBarang(); 

        return view('admin.pembelian.create', compact('suppliers', 'barangs', 'kategoriBarang', 'satuan', 'nextId', 'nextIdBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|string|unique:pembelian,id_pembelian',
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'jenis_pembayaran' => 'required|in:Cash,Kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0',
            'details.*.kuantitas' => 'required|integer|min:1',
        ]);

        $pembelian = Pembelian::create([
            'id_pembelian' => $request->id_pembelian,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'id_supplier' => $request->id_supplier,
            'id_user' => Auth::user()->id_user,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'jumlah_bayar' => $request->jumlah_bayar,
        ]);

        // Simpan detail (hold stok/harga beli, tidak update di sini)
        if ($request->details) {
            foreach ($request->details as $detail) {
                DetailPembelian::create([
                    'id_detail_pembelian' => $this->generateNextDetailId(),
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'harga_beli' => $detail['harga_beli'],
                    'kuantitas' => $detail['kuantitas'],
                    'sub_total' => $detail['harga_beli'] * $detail['kuantitas'], // Hitung sub_total
                ]);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function edit($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        // Jika sudah selesai, redirect atau disable
        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diedit.');
        }

        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all();
        $satuan = Satuan::all();
        $nextIdBarang = $this->generateNextIdBarang();

        return view('admin.pembelian.edit', compact('pembelian', 'suppliers', 'barangs', 'kategoriBarang', 'satuan', 'nextIdBarang'));
    }

    public function update(Request $request, $id_pembelian)
    {
        $request->validate([
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'jenis_pembayaran' => 'required|in:Cash,Kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0',
            'details.*.kuantitas' => 'required|integer|min:1',
            'details.*.id_detail_pembelian' => 'nullable|exists:detail_pembelian,id_detail_pembelian',
        ]);

        $pembelian = Pembelian::findOrFail($id_pembelian);
        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diupdate.');
        }

        $pembelian->update($request->only(['tanggal_pembelian', 'id_supplier', 'jenis_pembayaran', 'jumlah_bayar']));

        $existingDetailIds = $pembelian->detailPembelian->pluck('id_detail_pembelian')->toArray();
        $submittedDetailIds = array_filter(array_column($request->details ?? [], 'id_detail_pembelian'));

        DetailPembelian::where('id_pembelian', $pembelian->id_pembelian)
            ->whereNotIn('id_detail_pembelian', $submittedDetailIds)
            ->delete();

        if ($request->details) {
            foreach ($request->details as $detail) {
                $detailData = [
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'harga_beli' => $detail['harga_beli'],
                    'kuantitas' => $detail['kuantitas'],
                    'sub_total' => $detail['harga_beli'] * $detail['kuantitas'],
                ];

                if (!empty($detail['id_detail_pembelian'])) {
                    DetailPembelian::where('id_detail_pembelian', $detail['id_detail_pembelian'])
                        ->update($detailData);
                } else {
                    $detailData['id_detail_pembelian'] = $this->generateNextDetailId();
                    DetailPembelian::create($detailData);
                }
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy($id_pembelian)
    {
        $pembelian = Pembelian::findOrFail($id_pembelian);

        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat dihapus.');
        }

        $pembelian->detailPembelian()->delete();
        $pembelian->delete();

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
    }

    public function selesai($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai.');
        }

        $pembelian->update(['tanggal_terima' => now()]);

        // Update stok, harga_beli, dan retail
        foreach ($pembelian->detailPembelian as $detail) {
            $barang = Barang::find($detail->id_barang);
            if ($barang) {
                $barang->increment('stok', $detail->kuantitas);
                $barang->update(['harga_beli' => $detail->harga_beli]); // Update harga beli
                // Panggil weighted average di BarangController
                app(BarangController::class)->updateRetail($detail->id_barang, $detail->kuantitas, $detail->harga_beli);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Pembelian ditandai selesai dan stok diperbarui.');
    }

    private function generateNextId()
    {
        $maxNum = Pembelian::selectRaw('MAX(CAST(SUBSTRING(id_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'PEM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateNextDetailId()
    {
        $maxNum = DetailPembelian::selectRaw('MAX(CAST(SUBSTRING(id_detail_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'DET' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateNextIdBarang()
    {
        $maxNum = Barang::selectRaw('MAX(CAST(SUBSTRING(id_barang, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'BRG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function storeBarang(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
            'id_supplier_barang' => 'required|exists:supplier,id_supplier',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'merk_barang' => 'nullable|string|max:255',
            'berat' => 'required|numeric|min:0.01',
        ]);

        $barang = Barang::create([
            'id_barang' => $this->generateNextIdBarang(),
            'nama_barang' => $request->nama_barang,
            'id_kategori_barang' => $request->id_kategori_barang,
            'id_supplier' => $request->id_supplier_barang,
            'id_satuan' => $request->id_satuan,
            'merk_barang' => $request->merk_barang ?: '',
            'berat' => $request->berat,
            'harga_beli' => 0,
            'stok' => 0,
            'retail' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang baru berhasil ditambahkan.',
            'barang' => $barang
        ]);
    }
}