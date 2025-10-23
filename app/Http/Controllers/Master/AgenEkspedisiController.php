<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgenEkspedisi;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Kota;

class AgenEkspedisiController extends Controller
{
    /**
     * Index: list agen ekspedisi dengan search dan per_page
     */
    public function index(Request $request)
    {
        $query = AgenEkspedisi::with(['negara', 'provinsi', 'kota']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ekspedisi', 'like', "%{$search}%")
                  ->orWhere('id_ekspedisi', 'like', "%{$search}%")
                  ->orWhere('email_ekspedisi', 'like', "%{$search}%")
                  ->orWhere('telepon_ekspedisi', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $agens = $query->orderBy('id_ekspedisi', 'asc')->paginate($perPage);

        return view('master.data-agen-ekspedisi.index', ['agens' => $agens]);
    }

    /**
     * Form create (dengan next ID dan dropdown)
     */
    public function create()
    {
        $nextId = $this->generateNextId();
        $negara = Negara::all();
        $provinsi = Provinsi::all();
        $kota = Kota::all();

        return view('master.data-agen-ekspedisi.create', compact('nextId', 'negara', 'provinsi', 'kota'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255|unique:agen_ekspedisi,nama_ekspedisi',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_ekspedisi' => 'required|string|max:16',
            'email_ekspedisi' => 'nullable|email|max:255|unique:agen_ekspedisi,email_ekspedisi',
        ], [
            'nama_ekspedisi.required' => 'Nama ekspedisi wajib diisi.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'telepon_ekspedisi.required' => 'Telepon wajib diisi.',
            'email_ekspedisi.email' => 'Format email tidak valid.',
        ]);

        AgenEkspedisi::create([
            'id_ekspedisi' => $this->generateNextId(),
            'nama_ekspedisi' => $request->nama_ekspedisi,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'telepon_ekspedisi' => $request->telepon_ekspedisi,
            'email_ekspedisi' => $request->email_ekspedisi,
        ]);

        return redirect()->route('master.data-agen-ekspedisi.index')
                         ->with('success', 'Data agen ekspedisi berhasil ditambahkan.');
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $agen = AgenEkspedisi::findOrFail($id);
        $negara = Negara::all();
        $provinsi = Provinsi::all();
        $kota = Kota::all();

        return view('master.data-agen-ekspedisi.edit', compact('agen', 'negara', 'provinsi', 'kota'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255|unique:agen_ekspedisi,nama_ekspedisi,' . $id . ',id_ekspedisi',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_ekspedisi' => 'required|string|max:16',
            'email_ekspedisi' => 'nullable|email|max:255|unique:agen_ekspedisi,email_ekspedisi,' . $id . ',id_ekspedisi',
        ]);

        $agen = AgenEkspedisi::findOrFail($id);
        $agen->update($request->only([
            'nama_ekspedisi',
            'id_negara',
            'id_provinsi',
            'id_kota',
            'telepon_ekspedisi',
            'email_ekspedisi',
        ]));

        return redirect()->route('master.data-agen-ekspedisi.index')
                         ->with('success', 'Data agen ekspedisi berhasil diperbarui.');
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        $agen = AgenEkspedisi::findOrFail($id);
        $agen->delete();

        return redirect()->route('master.data-agen-ekspedisi.index')
                         ->with('success', 'Data agen ekspedisi berhasil dihapus.');
    }

    /**
     * Generate next ID format AE001, AE002, ...
     */
    private function generateNextId()
    {
        $maxNum = AgenEkspedisi::selectRaw('MAX(CAST(SUBSTRING(id_ekspedisi, 3) AS UNSIGNED)) as max_num')
                      ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'AE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
