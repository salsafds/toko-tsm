<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_satuan', 'like', "%{$search}%")
                ->orWhere('id_satuan', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $satuans = $query->paginate($perPage);

        return view('master.data-satuan.index', compact('satuans'));
    }

    public function create()
    {
       // Generate preview ID untuk form (berurutan)
        $nextId = $this->generateNextId();
        return view('master.data-satuan.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
        ]);

        Satuan::create([
            'id_satuan' => $this->generateNextId(),  // Gunakan ID berurutan
            'nama_satuan' => $request->nama_satuan,
        ]);


        return redirect()->route('master.data-satuan.index')
                         ->with('success', 'Data satuan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('master.data-satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan,' . $id . ',id_satuan',
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return redirect()->route('master.data-satuan.index')
                        ->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();

        return redirect()->route('master.data-satuan.index')
                         ->with('success', 'Data satuan berhasil dihapus.');
    }
    /**
     * Generate ID satuan berurutan (ST0001, ST0002, dll.)
     */
    private function generateNextId()
    {
        // Ambil MAX angka dari id_satuan (SUBSTRING setelah 'ST', cast ke UNSIGNED)
        // Jika table kosong, maxNum = null → fallback ke 0
        $maxNum = Satuan::selectRaw('MAX(CAST(SUBSTRING(id_satuan, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format: ST + 4 digit dengan leading zero (ST0001, ST0002, ..., ST0100, dll.)
        return 'ST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

