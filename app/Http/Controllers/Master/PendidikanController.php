<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;

class PendidikanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendidikan::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('tingkat_pendidikan', 'like', "%{$search}%")
                ->orWhere('id_pendidikan', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $pendidikans = $query->paginate($perPage);

        return view('master.data-pendidikan.index', compact('pendidikans'));
    }

    public function create()
    {
        // Generate preview ID 
        $nextId = $this->generateNextId();
        return view('master.data-pendidikan.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat_pendidikan' => 'required|string|max:100|unique:pendidikan,tingkat_pendidikan',
        ], [
            'tingkat_pendidikan.required' => 'Tingkat pendidikan wajib diisi.',
            'tingkat_pendidikan.string' => 'Tingkat pendidikan harus berupa teks.',
            'tingkat_pendidikan.max' => 'Tingkat pendidikan tidak boleh lebih dari 50 karakter.',
            'tingkat_pendidikan.unique' => 'Tingkat pendidikan sudah digunakan.',
        ]);

        Pendidikan::create([
            'id_pendidikan' => $this->generateNextId(),
            'tingkat_pendidikan' => $request->tingkat_pendidikan,
        ]);

        return redirect()->route('master.data-pendidikan.index')
                         ->with('success', 'Data pendidikan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pendidikan = Pendidikan::findOrFail($id);
        return view('master.data-pendidikan.edit', compact('pendidikan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tingkat_pendidikan' => 'required|string|max:100|unique:pendidikan,tingkat_pendidikan,' . $id . ',id_pendidikan',
        ], [
            'tingkat_pendidikan.required' => 'Tingkat pendidikan wajib diisi.',
            'tingkat_pendidikan.string' => 'Tingkat pendidikan harus berupa teks.',
            'tingkat_pendidikan.max' => 'Tingkat pendidikan tidak boleh lebih dari 50 karakter.',
            'tingkat_pendidikan.unique' => 'Tingkat pendidikan sudah digunakan.',
        ]);

        $pendidikan = Pendidikan::findOrFail($id);
        $pendidikan->update([
            'tingkat_pendidikan' => $request->tingkat_pendidikan,
        ]);

        return redirect()->route('master.data-pendidikan.index')
                        ->with('success', 'Data pendidikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pendidikan = Pendidikan::findOrFail($id);
        $pendidikan->delete();

        return redirect()->route('master.data-pendidikan.index')
                         ->with('success', 'Data pendidikan berhasil dihapus.');
    }

    //GENERATE ID PENDIDIKAN
    private function generateNextId()
    {
        $maxNum = Pendidikan::selectRaw('MAX(CAST(SUBSTRING(id_pendidikan, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format ID: PD + 2 digit
        return 'PD' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}