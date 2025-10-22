<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kota;
use App\Models\Negara;
use App\Models\Provinsi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kota::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kota', 'like', "%{$search}%")
                ->orWhere('id_kota', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $kotas = $query->paginate($perPage);

        return view('master.data-kota.index', compact('kotas'));
    }

    public function create()
    {
        // Generate preview ID untuk form (berurutan)
        $nextId = $this->generateNextId();

        // ambil daftar negara & provinsi untuk dropdown
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = Provinsi::orderBy('nama_provinsi')->get();

        return view('master.data-kota.create', compact('nextId', 'negara', 'provinsi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kota'  => 'required|string|max:100|unique:kota,nama_kota',
            'id_negara'  => 'required|exists:negara,id_negara',
            'id_provinsi'=> 'required|exists:provinsi,id_provinsi',
        ], [
            'nama_kota.required' => 'Nama kota wajib diisi.',
            'nama_kota.string' => 'Nama kota harus berupa teks.',
            'nama_kota.max' => 'Nama kota tidak boleh lebih dari 100 karakter.',
            'nama_kota.unique' => 'Nama kota sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
        ]);

        Kota::create([
            'id_kota' => $this->generateNextId(),
            'nama_kota' => $request->nama_kota,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
        ]);

        return redirect()->route('master.data-kota.index')
                         ->with('success', 'Data kota berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kota = Kota::findOrFail($id);

        // ambil daftar negara & provinsi untuk dropdown
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = Provinsi::orderBy('nama_provinsi')->get();

        return view('master.data-kota.edit', compact('kota', 'negara', 'provinsi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kota'  => 'required|string|max:100|unique:kota,nama_kota,' . $id . ',id_kota',
            'id_negara'  => 'required|exists:negara,id_negara',
            'id_provinsi'=> 'required|exists:provinsi,id_provinsi',
        ], [
            'nama_kota.required' => 'Nama kota wajib diisi.',
            'nama_kota.string' => 'Nama kota harus berupa teks.',
            'nama_kota.max' => 'Nama kota tidak boleh lebih dari 100 karakter.',
            'nama_kota.unique' => 'Nama kota sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
        ]);

        $kota = Kota::findOrFail($id);
        $kota->update([
            'nama_kota' => $request->nama_kota,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
        ]);

        return redirect()->route('master.data-kota.index')
                        ->with('success', 'Data kota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kota = Kota::findOrFail($id);
        $kota->delete();

        return redirect()->route('master.data-kota.index')
                         ->with('success', 'Data kota berhasil dihapus.');
    }

    /**
     * Generate ID kota berurutan (KT001, KT002, dll.)
     */
    private function generateNextId()
    {
        // Ambil MAX angka dari id_kota (SUBSTRING setelah 'KT', cast ke UNSIGNED)
        // Jika table kosong, maxNum = null → fallback ke 0
        $maxNum = Kota::selectRaw('MAX(CAST(SUBSTRING(id_kota, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format: KT + 3 digit dengan leading zero (KT001, KT002, ..., KT100, dll.)
        return 'KT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
