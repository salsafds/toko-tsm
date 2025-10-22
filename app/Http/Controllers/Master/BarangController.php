<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Barang::query()->with(['kategoriBarang', 'satuan']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('id_barang', 'like', "%{$search}%")
                  ->orWhereHas('kategoriBarang', function ($q) use ($search) {
                      $q->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->query('per_page', 10);
        $barang = $query->paginate($perPage);

        return view('master.data-barang.index', compact('barang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriBarang = KategoriBarang::all();
        $supplier = Supplier::all();
        $satuan = Satuan::all();
        $nextId = $this->generateNextId();

        return view('master.data-barang.create', compact('kategoriBarang', 'supplier', 'satuan', 'nextId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|string|unique:barang,id_barang',
            'nama_barang' => 'required|string|max:100',
            'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'merk_barang' => 'nullable|string|max:100',
            'berat' => 'required|numeric|min:0.01',
            'harga_beli' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        Barang::create([
            'id_barang' => $request->id_barang,
            'nama_barang' => $request->nama_barang,
            'id_kategori_barang' => $request->id_kategori_barang,
            'id_supplier' => $request->id_supplier,
            'id_satuan' => $request->id_satuan,
            'merk_barang' => $request->merk_barang ?: '',
            'berat' => $request->berat,
            'harga_beli' => $request->harga_beli,
            'stok' => $request->stok,
            'retail' => $request->harga_beli * 1.2,
        ]);

        return redirect()->route('master.data-barang.index')
                        ->with('success', 'Data barang berhasil ditambahkan.');

        }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        $kategoriBarang = KategoriBarang::all();
        $supplier = Supplier::all();
        $satuan = Satuan::all();

        return view('master.data-barang.edit', compact('barang', 'kategoriBarang', 'supplier', 'satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'merk_barang' => 'nullable|string|max:100',
            'berat' => 'required|numeric|min:0.01',
            'harga_beli' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'nama_barang.max' => 'Nama barang tidak boleh lebih dari 100 karakter.',
            'id_kategori_barang.required' => 'Kategori barang wajib dipilih.',
            'id_kategori_barang.exists' => 'Kategori barang tidak valid.',
            'id_supplier.required' => 'Supplier wajib dipilih.',
            'id_supplier.exists' => 'Supplier tidak valid.',
            'id_satuan.required' => 'Satuan wajib dipilih.',
            'id_satuan.exists' => 'Satuan tidak valid.',
            'merk_barang.string' => 'Merk barang harus berupa teks.',
            'merk_barang.max' => 'Merk barang tidak boleh lebih dari 100 karakter.',
            'berat.required' => 'Berat wajib diisi.',
            'berat.numeric' => 'Berat harus berupa angka.',
            'berat.min' => 'Berat harus lebih dari 0.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_beli.numeric' => 'Harga beli harus berupa angka.',
            'harga_beli.min' => 'Harga beli tidak boleh negatif.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
        ]);

        $barang = Barang::findOrFail($id_barang);
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'id_kategori_barang' => $request->id_kategori_barang,
            'id_supplier' => $request->id_supplier,
            'id_satuan' => $request->id_satuan,
            'merk_barang' => $request->merk_barang ?: '',
            'berat' => $request->berat,
            'harga_beli' => $request->harga_beli,
            'stok' => $request->stok,
            'retail' => $request->harga_beli * 1.2, // Example: 20% markup
        ]);

        return redirect()->route('master.data-barang.index')
                        ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        $barang->delete();

        return redirect()->route('master.data-barang.index')
                        ->with('success', 'Data barang berhasil dihapus.');
    }

    /**
     * Generate ID barang berurutan (BRG0001, BRG0002, dll.)
     */
    private function generateNextId()
    {
        $maxNum = Barang::selectRaw('MAX(CAST(SUBSTRING(id_barang, 4) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'BRG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}