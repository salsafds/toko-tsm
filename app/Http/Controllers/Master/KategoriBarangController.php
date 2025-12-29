<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriBarang;

class KategoriBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriBarang::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('id_kategori_barang', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $kategoris = $query->paginate($perPage);

        return view('master.data-kategori-barang.index', compact('kategoris'));
    }

    public function create()
    {
        // Generate preview ID 
        $nextId = $this->generateNextId();
        return view('master.data-kategori-barang.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_barang,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari 50 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        KategoriBarang::create([
            'id_kategori_barang' => $this->generateNextId(),
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('master.data-kategori-barang.index')
                         ->with('success', 'Data kategori barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        return view('master.data-kategori-barang.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_barang,nama_kategori,' . $id . ',id_kategori_barang',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari 50 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $kategori = KategoriBarang::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('master.data-kategori-barang.index')
                         ->with('success', 'Data kategori barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        $kategori->delete();

        return redirect()->route('master.data-kategori-barang.index')
                         ->with('success', 'Data kategori barang berhasil dihapus.');
    }

    //GENERATE ID KATEGORI BARANG
    private function generateNextId()
    {
        $maxNum = KategoriBarang::selectRaw('MAX(CAST(SUBSTRING(id_kategori_barang, 4) AS UNSIGNED)) as max_num')
                               ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        return 'KB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}