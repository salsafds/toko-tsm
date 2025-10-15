<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Negara;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NegaraController extends Controller
{
    public function index(Request $request)
    {
        $query = Negara::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_negara', 'like', "%{$search}%")
                ->orWhere('id_negara', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $negaras = $query->paginate($perPage);

        return view('master.data-negara.index', compact('negaras'));
    }

    public function create()
    {
        // Generate preview ID untuk form (berurutan)
        $nextId = $this->generateNextId();
        return view('master.data-negara.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_negara' => 'required|string|max:100|unique:negara,nama_negara',
        ], [
            'nama_negara.required' => 'Nama negara wajib diisi.',
            'nama_negara.string' => 'Nama negara harus berupa teks.',
            'nama_negara.max' => 'Nama negara tidak boleh lebih dari 50 karakter.',
            'nama_negara.unique' => 'Nama negara sudah digunakan.',
        ]);

        Negara::create([
            'id_negara' => $this->generateNextId(),
            'nama_negara' => $request->nama_negara,
        ]);

        return redirect()->route('master.data-negara.index')
                         ->with('success', 'Data negara berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $negara = Negara::findOrFail($id);
        return view('master.data-negara.edit', compact('negara'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_negara' => 'required|string|max:100|unique:negara,nama_negara,' . $id . ',id_negara',
        ], [
            'nama_negara.required' => 'Nama negara wajib diisi.',
            'nama_negara.string' => 'Nama negara harus berupa teks.',
            'nama_negara.max' => 'Nama negara tidak boleh lebih dari 50 karakter.',
            'nama_negara.unique' => 'Nama negara sudah digunakan.',
        ]);

        $negara = Negara::findOrFail($id);
        $negara->update([
            'nama_negara' => $request->nama_negara,
        ]);

        return redirect()->route('master.data-negara.index')
                        ->with('success', 'Data negara berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $negara = Negara::findOrFail($id);
        $negara->delete();

        return redirect()->route('master.data-negara.index')
                         ->with('success', 'Data negara berhasil dihapus.');
    }

    /**
     * Generate ID negara berurutan (ST0001, ST0002, dll.)
     */
    private function generateNextId()
    {
        // Ambil MAX angka dari id_negara (SUBSTRING setelah 'ST', cast ke UNSIGNED)
        // Jika table kosong, maxNum = null → fallback ke 0
        $maxNum = Negara::selectRaw('MAX(CAST(SUBSTRING(id_negara, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format: ST + 4 digit dengan leading zero (ST0001, ST0002, ..., ST0100, dll.)
        return 'ST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}