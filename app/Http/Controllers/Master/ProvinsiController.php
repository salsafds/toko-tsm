<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProvinsiController extends Controller
{
    public function index(Request $request)
    {
        $query = Provinsi::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_provinsi', 'like', "%{$search}%")
                ->orWhere('id_provinsi', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $provinsis = $query->paginate($perPage);

        return view('master.data-provinsi.index', compact('provinsis'));
    }

    public function create()
    {
        // Generate preview ID untuk form (berurutan)
        $nextId = $this->generateNextId();
        return view('master.data-provinsi.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100|unique:provinsi,nama_provinsi',
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'nama_provinsi.string' => 'Nama provinsi harus berupa teks.',
            'nama_provinsi.max' => 'Nama provinsi tidak boleh lebih dari 50 karakter.',
            'nama_provinsi.unique' => 'Nama provinsi sudah digunakan.',
        ]);

        Provinsi::create([
            'id_provinsi' => $this->generateNextId(),
            'nama_provinsi' => $request->nama_provinsi,
        ]);

        return redirect()->route('master.data-provinsi.index')
                         ->with('success', 'Data provinsi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $provinsi = Provinsi::findOrFail($id);
        return view('master.data-provinsi.edit', compact('provinsi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100|unique:provinsi,nama_provinsi,' . $id . ',id_provinsi',
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'nama_provinsi.string' => 'Nama provinsi harus berupa teks.',
            'nama_provinsi.max' => 'Nama provinsi tidak boleh lebih dari 50 karakter.',
            'nama_provinsi.unique' => 'Nama provinsi sudah digunakan.',
        ]);

        $provinsi = Provinsi::findOrFail($id);
        $provinsi->update([
            'nama_provinsi' => $request->nama_provinsi,
        ]);

        return redirect()->route('master.data-provinsi.index')
                        ->with('success', 'Data provinsi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $provinsi = Provinsi::findOrFail($id);
        $provinsi->delete();

        return redirect()->route('master.data-provinsi.index')
                         ->with('success', 'Data provinsi berhasil dihapus.');
    }

    /**
     * Generate ID provinsi berurutan (ST0001, ST0002, dll.)
     */
    private function generateNextId()
    {
        // Ambil MAX angka dari id_provinsi (SUBSTRING setelah 'ST', cast ke UNSIGNED)
        // Jika table kosong, maxNum = null → fallback ke 0
        $maxNum = Provinsi::selectRaw('MAX(CAST(SUBSTRING(id_provinsi, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format: ST + 4 digit dengan leading zero (ST0001, ST0002, ..., ST0100, dll.)
        return 'ST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}