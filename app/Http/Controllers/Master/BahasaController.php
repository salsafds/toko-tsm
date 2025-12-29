<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bahasa;

class BahasaController extends Controller
{
    public function index(Request $request)
    {
        $query = Bahasa::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahasa', 'like', "%{$search}%")
                ->orWhere('id_bahasa', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $bahasas = $query->paginate($perPage);

        return view('master.data-bahasa.index', compact('bahasas'));
    }

    public function create()
    {
        // Generate ID
        $nextId = $this->generateNextId();
        return view('master.data-bahasa.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahasa' => 'required|string|max:50|unique:bahasa,nama_bahasa',
        ], [
            'nama_bahasa.required' => 'Nama bahasa wajib diisi.',
            'nama_bahasa.string' => 'Nama bahasa harus berupa teks.',
            'nama_bahasa.max' => 'Nama bahasa tidak boleh lebih dari 50 karakter.',
            'nama_bahasa.unique' => 'Nama bahasa sudah digunakan.',
        ]);

        Bahasa::create([
            'id_bahasa' => $this->generateNextId(),
            'nama_bahasa' => $request->nama_bahasa,
        ]);

        return redirect()->route('master.data-bahasa.index')
                         ->with('success', 'Data bahasa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $bahasa = Bahasa::findOrFail($id);
        return view('master.data-bahasa.edit', compact('bahasa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bahasa' => 'required|string|max:100|unique:bahasa,nama_bahasa,' . $id . ',id_bahasa',
        ], [
            'nama_bahasa.required' => 'Nama bahasa wajib diisi.',
            'nama_bahasa.string' => 'Nama bahasa harus berupa teks.',
            'nama_bahasa.max' => 'Nama bahasa tidak boleh lebih dari 50 karakter.',
            'nama_bahasa.unique' => 'Nama bahasa sudah digunakan.',
        ]);

        $bahasa = Bahasa::findOrFail($id);
        $bahasa->update([
            'nama_bahasa' => $request->nama_bahasa,
        ]);

        return redirect()->route('master.data-bahasa.index')
                        ->with('success', 'Data bahasa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bahasa = Bahasa::findOrFail($id);
        $bahasa->delete();

        return redirect()->route('master.data-bahasa.index')
                         ->with('success', 'Data bahasa berhasil dihapus.');
    }

    private function generateNextId()
    {
        $maxNum = Bahasa::selectRaw('MAX(CAST(SUBSTRING(id_bahasa, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format ID: BS + 2 digit 
        return 'BS' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}