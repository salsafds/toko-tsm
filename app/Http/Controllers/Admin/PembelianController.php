<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Barang;
use App\Models\KategoriBarang; // Asumsikan ada
use App\Models\Satuan; // Asumsikan ada
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query()->with(['supplier', 'user', 'detailPembelian']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_pembelian', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('nama_supplier', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
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
        $users = User::all();
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all(); // Untuk form tambah barang
        $satuan = Satuan::all(); // Untuk form tambah barang
        $nextId = $this->generateNextId();
        $nextIdBarang = $this->generateNextIdBarang(); // Untuk preview ID barang

        return view('admin.pembelian.create', compact('suppliers', 'users', 'barangs', 'kategoriBarang', 'satuan', 'nextId', 'nextIdBarang'));
    }

    public function store(Request $request)
    {
        // Validasi utama
        $request->validate([
            'id_pembelian' => 'required|string|unique:pembelian,id_pembelian',
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_user' => 'required|exists:users,id',
            'jenis_pembayaran' => 'required|in:cash,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0', // Tambah validasi harga_beli
            'details.*.kuantitas' => 'required|integer|min:1',
        ]);

        // Jika checkbox tambah barang dicentang, simpan barang baru dulu
        if ($request->has('tambah_barang') && $request->tambah_barang == '1') {
            $request->validate([
                'nama_barang' => 'required|string',
                'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
                'id_supplier_barang' => 'required|exists:supplier,id_supplier',
                'id_satuan' => 'required|exists:satuan,id_satuan',
                'merk_barang' => 'nullable|string',
                'berat' => 'required|numeric|min:0',
            ]);

            $barang = Barang::create([
                'id_barang' => $this->generateNextIdBarang(),
                'nama_barang' => $request->nama_barang,
                'id_kategori_barang' => $request->id_kategori_barang,
                'id_supplier' => $request->id_supplier_barang,
                'id_satuan' => $request->id_satuan,
                'merk_barang' => $request->merk_barang,
                'berat' => $request->berat,
                // Field lain seperti harga_jual, stok diisi default atau di form pembelian
            ]);

            // Reload barangs untuk dropdown
            $barangs = Barang::all();
        }

        // Simpan pembelian
        $pembelian = Pembelian::create([
            'id_pembelian' => $request->id_pembelian,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'id_supplier' => $request->id_supplier,
            'id_user' => $request->id_user,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'jumlah_bayar' => $request->jumlah_bayar,
        ]);

        // Simpan detail (status hold, stok belum update)
        if ($request->details) {
            foreach ($request->details as $detail) {
                DetailPembelian::create([
                    'id_detail_pembelian' => $this->generateNextDetailId(),
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'harga_beli' => $detail['harga_beli'], // Tambah
                    'kuantitas' => $detail['kuantitas'],
                    // sub_total dihitung otomatis di model
                ]);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function edit($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        // Jika sudah selesai, redirect atau disable
        if ($pembelian->isSelesai()) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diedit.');
        }

        $suppliers = Supplier::all();
        $users = User::all();
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all();
        $satuan = Satuan::all();
        $nextIdBarang = $this->generateNextIdBarang();

        return view('admin.pembelian.edit', compact('pembelian', 'suppliers', 'users', 'barangs', 'kategoriBarang', 'satuan', 'nextIdBarang'));
    }

    public function update(Request $request, $id_pembelian)
    {
        $request->validate([
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_user' => 'required|exists:users,id',
            'jenis_pembayaran' => 'required|in:cash,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0',
            'details.*.kuantitas' => 'required|integer|min:1',
        ]);

        $pembelian = Pembelian::findOrFail($id_pembelian);

        if ($pembelian->isSelesai()) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diupdate.');
        }

        $pembelian->update($request->only(['tanggal_pembelian', 'id_supplier', 'id_user', 'jenis_pembayaran', 'jumlah_bayar']));

        // Update details (hapus lama, tambah baru)
        $pembelian->detailPembelian()->delete();
        if ($request->details) {
            foreach ($request->details as $detail) {
                DetailPembelian::create([
                    'id_detail_pembelian' => $this->generateNextDetailId(),
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'harga_beli' => $detail['harga_beli'],
                    'kuantitas' => $detail['kuantitas'],
                ]);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy($id_pembelian)
    {
        $pembelian = Pembelian::findOrFail($id_pembelian);

        if ($pembelian->isSelesai()) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat dihapus.');
        }

        $pembelian->detailPembelian()->delete();
        $pembelian->delete();

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
    }

    public function selesai($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        if ($pembelian->isSelesai()) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai.');
        }

        $pembelian->update(['tanggal_terima' => now()]);

        // Update stok barang
        foreach ($pembelian->detailPembelian as $detail) {
            $barang = Barang::find($detail->id_barang);
            if ($barang) {
                $barang->increment('stok', $detail->kuantitas);
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
        // Asumsikan format ID barang, misal 'BRG0001'
        $maxNum = Barang::selectRaw('MAX(CAST(SUBSTRING(id_barang, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'BRG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}