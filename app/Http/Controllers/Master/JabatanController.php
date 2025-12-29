<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_jabatan', 'like', "%{$search}%")
                ->orWhere('id_jabatan', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $jabatans = $query->paginate($perPage);

        return view('master.data-jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        // Generate preview ID untuk form (berurutan)
        $nextId = $this->generateNextId();
        return view('master.data-jabatan.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100|unique:jabatan,nama_jabatan',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.string' => 'Nama jabatan harus berupa teks.',
            'nama_jabatan.max' => 'Nama jabatan tidak boleh lebih dari 100 karakter.',
            'nama_jabatan.unique' => 'Nama jabatan sudah digunakan.',
        ]);

        Jabatan::create([
            'id_jabatan' => $this->generateNextId(),
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return redirect()->route('master.data-jabatan.index')
                         ->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('master.data-jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100|unique:jabatan,nama_jabatan,' . $id . ',id_jabatan',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.string' => 'Nama jabatan harus berupa teks.',
            'nama_jabatan.max' => 'Nama jabatan tidak boleh lebih dari 50 karakter.',
            'nama_jabatan.unique' => 'Nama jabatan sudah digunakan.',
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return redirect()->route('master.data-jabatan.index')
                        ->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $jabatan->delete();

        return redirect()->route('master.data-jabatan.index')
                         ->with('success', 'Data jabatan berhasil dihapus.');
    }

    //GENERATE ID JABATAN
    private function generateNextId()
    {
        $maxNum = Jabatan::selectRaw('MAX(CAST(SUBSTRING(id_jabatan, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format ID: JB + 2 digit 
        return 'JB' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}