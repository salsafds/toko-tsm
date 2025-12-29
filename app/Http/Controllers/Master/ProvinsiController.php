<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Negara;

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

        // ambil daftar negara untuk dropdown
        $negara = Negara::orderBy('nama_negara')->get();

        return view('master.data-provinsi.create', compact('nextId', 'negara'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100|unique:provinsi,nama_provinsi',
            'id_negara'     => 'required|exists:negara,id_negara',
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'nama_provinsi.string' => 'Nama provinsi harus berupa teks.',
            'nama_provinsi.max' => 'Nama provinsi tidak boleh lebih dari 50 karakter.',
            'nama_provinsi.unique' => 'Nama provinsi sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
        ]);

        Provinsi::create([
            'id_provinsi' => $this->generateNextId(),
            'nama_provinsi' => $request->nama_provinsi,
            'id_negara' => $request->id_negara,
        ]);

        return redirect()->route('master.data-provinsi.index')
                         ->with('success', 'Data provinsi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $provinsi = Provinsi::findOrFail($id);

        // ambil daftar negara untuk dropdown
        $negara = Negara::orderBy('nama_negara')->get();

        return view('master.data-provinsi.edit', compact('provinsi', 'negara'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_provinsi' => 'required|string|max:100|unique:provinsi,nama_provinsi,' . $id . ',id_provinsi',
            'id_negara'     => 'required|exists:negara,id_negara',
        ], [
            'nama_provinsi.required' => 'Nama provinsi wajib diisi.',
            'nama_provinsi.string' => 'Nama provinsi harus berupa teks.',
            'nama_provinsi.max' => 'Nama provinsi tidak boleh lebih dari 50 karakter.',
            'nama_provinsi.unique' => 'Nama provinsi sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
        ]);

        $provinsi = Provinsi::findOrFail($id);
        $provinsi->update([
            'nama_provinsi' => $request->nama_provinsi,
            'id_negara' => $request->id_negara,
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

    //Return JSON list provinsi .
    public function provinsiByNegara($id_negara)
    {
        $provinsis = \App\Models\Provinsi::where('id_negara', $id_negara)
                    ->orderBy('nama_provinsi', 'asc')
                    ->get(['id_provinsi', 'nama_provinsi']);

        return response()->json($provinsis);
    }


    //GENERATE ID PROVINSI
    private function generateNextId()
    {
        $maxNum = Provinsi::selectRaw('MAX(CAST(SUBSTRING(id_provinsi, 3) AS UNSIGNED)) as max_num')
                        ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        
        // Format ID: PV + 3 digit
        return 'PV' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
