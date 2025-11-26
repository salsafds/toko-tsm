<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    
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

        return view('admin.data-barang.index', compact('barang'));
    }

    public function create()
    {
        $kategoriBarang = KategoriBarang::all();
        $supplier = Supplier::all();
        $satuan = Satuan::all();
        $nextId = $this->generateNextId();

        return view('admin.data-barang.create', compact('kategoriBarang', 'supplier', 'satuan', 'nextId'));
    }

    public function store(Request $request)
    {

        if (Barang::where('nama_barang', $request->nama_barang)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan nama ini sudah ada.'
            ], 409);
        }

        
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'berat' => 'required|numeric|min:0.01',
            'margin' => 'nullable|numeric|min:0|max:100', 
        ]);

  
         $nextId = $this->generateNextId();

    Barang::create([
        'id_barang' => $nextId,
        'nama_barang' => $request->nama_barang,
        'id_kategori_barang' => $request->id_kategori_barang,
        'id_supplier' => $request->id_supplier,
        'id_satuan' => $request->id_satuan,
        'merk_barang' => $request->merk_barang ?: '',
        'berat' => $request->berat,
        'harga_beli' => 0,
        'stok' => 0,
        'retail' => 0,
        'margin' => $request->margin ?? 0,
    ]);
    
    return redirect()->route('admin.data-barang.index')
                    ->with('success', 'Barang berhasil ditambahkan.');
}

    public function edit($id_barang)
    {
        
        $barang = Barang::findOrFail($id_barang);
        $kategoriBarang = KategoriBarang::all();
        $supplier = Supplier::all();
        $satuan = Satuan::all();

        return view('admin.data-barang.edit', compact('barang', 'kategoriBarang', 'supplier', 'satuan'));
    }

    public function update(Request $request, $id_barang)
{
    
    $request->validate([
        'nama_barang' => 'required|string|max:100',
        'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
        'id_supplier' => 'required|exists:supplier,id_supplier',
        'id_satuan' => 'required|exists:satuan,id_satuan',
        'merk_barang' => 'nullable|string|max:100',
        'berat' => 'required|numeric|min:0.01',
        'margin' => 'nullable|numeric|min:0|max:100',
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
        'margin.numeric' => 'Margin harus berupa angka.',
        'margin.min' => 'Margin minimal 0.',
        'margin.max' => 'Margin maksimal 100.',
    ]);

    $barang = Barang::findOrFail($id_barang);

    
    $marginLama = $barang->margin ?? 0;


    $barang->update([
        'nama_barang' => $request->nama_barang,
        'id_kategori_barang' => $request->id_kategori_barang,
        'id_supplier' => $request->id_supplier,
        'id_satuan' => $request->id_satuan,
        'merk_barang' => $request->merk_barang ?: '',
        'berat' => $request->berat,
        'margin' => $request->margin ?? 0,
    ]);

    
    $marginBaru = $request->margin ?? 0;
    if ($marginLama != $marginBaru && $barang->harga_beli > 0) {
        $hargaRetailBaru = $barang->harga_beli * (1 + ($marginBaru / 100));
        $barang->update([
            'retail' => round($hargaRetailBaru, 2), 
        ]);
    }

    return redirect()->route('admin.data-barang.index')
                    ->with('success', 'Data barang berhasil diperbarui.');
    }


    public function destroy($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        $barang->delete();

        return redirect()->route('admin.data-barang.index')
                        ->with('success', 'Data barang berhasil dihapus.');
    }

    private function generateNextId()
    {
 
        $maxNum = Barang::selectRaw('MAX(CAST(SUBSTRING(id_barang, 4) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'BRG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function updateRetail($id_barang, $kuantitas_baru, $harga_beli_baru)
    {
        $barang = Barang::findOrFail($id_barang);

        $stok_lama = $barang->stok ?? 0;
        $harga_beli_lama = $barang->harga_beli ?? 0;

        $stok_total = $stok_lama + $kuantitas_baru;

        if ($stok_total <= 0) {
            $barang->update([
                'stok' => 0,
                'harga_beli' => 0,
                'retail' => 0,
            ]);
            return;
        }

        $total_nilai_lama = $stok_lama * $harga_beli_lama;
        $total_nilai_baru = $kuantitas_baru * $harga_beli_baru;
        $harga_beli_rata = ($total_nilai_lama + $total_nilai_baru) / $stok_total;
        
        $margin = $barang->margin ?? 0;
        $harga_retail_baru = $harga_beli_rata * (1 + ($margin / 100));

        $barang->update([
            'stok' => $stok_total,
            'harga_beli' => round($harga_beli_rata, 2),
            'retail' => round($harga_retail_baru, 2),
        ]);
    }
}
