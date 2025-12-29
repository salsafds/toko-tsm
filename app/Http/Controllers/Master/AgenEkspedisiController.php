<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Negara;
use App\Models\AgenEkspedisi;

class AgenEkspedisiController extends Controller
{
    public function index(Request $request)
    {
        $query = AgenEkspedisi::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ekspedisi', 'like', "%{$search}%")
                  ->orWhere('id_ekspedisi', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $agens = $query->paginate($perPage);

        return view('master.data-agen-ekspedisi.index', compact('agens'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = collect([]); 
        $kota = collect([]); 
        
        return view('master.data-agen-ekspedisi.create', compact('nextId', 'negara', 'provinsi', 'kota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255|unique:agen_ekspedisi,nama_ekspedisi',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_ekspedisi' => 'required|string|max:20',
            'email_ekspedisi' => 'nullable|email|max:100',
        ], [
            'nama_ekspedisi.required' => 'Nama agen ekspedisi wajib diisi.',
            'nama_ekspedisi.max' => 'Nama agen ekspedisi tidak boleh lebih dari 255 karakter.',
            'nama_ekspedisi.unique' => 'Nama agen ekspedisi sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'telepon_ekspedisi.required' => 'Telepon wajib diisi.',
            'telepon_ekspedisi.max' => 'Telepon tidak boleh lebih dari 20 karakter.',
            'email_ekspedisi.email' => 'Format email tidak valid.',
            'email_ekspedisi.max' => 'Email tidak boleh lebih dari 100 karakter.',
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

    public function edit($id)
    {
        $agen = AgenEkspedisi::findOrFail($id);
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = Provinsi::where('id_negara', $agen->id_negara)->orderBy('nama_provinsi')->get();
        $kota = Kota::where('id_provinsi', $agen->id_provinsi)->orderBy('nama_kota')->get();

        return view('master.data-agen-ekspedisi.edit', compact('agen', 'negara', 'provinsi', 'kota'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255|unique:agen_ekspedisi,nama_ekspedisi,' . $id . ',id_ekspedisi',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_ekspedisi' => 'required|string|max:20',
            'email_ekspedisi' => 'nullable|email|max:100',
        ], [
            'nama_ekspedisi.required' => 'Nama agen ekspedisi wajib diisi.',
            'nama_ekspedisi.max' => 'Nama agen ekspedisi tidak boleh lebih dari 255 karakter.',
            'nama_ekspedisi.unique' => 'Nama agen ekspedisi sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'telepon_ekspedisi.required' => 'Telepon wajib diisi.',
            'telepon_ekspedisi.max' => 'Telepon tidak boleh lebih dari 20 karakter.',
            'email_ekspedisi.email' => 'Format email tidak valid.',
            'email_ekspedisi.max' => 'Email tidak boleh lebih dari 100 karakter.',
        ]);

        $agen = AgenEkspedisi::findOrFail($id);
        $agen->update([
            'nama_ekspedisi' => $request->nama_ekspedisi,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'telepon_ekspedisi' => $request->telepon_ekspedisi,
            'email_ekspedisi' => $request->email_ekspedisi,
        ]);

        return redirect()->route('master.data-agen-ekspedisi.index')
                         ->with('success', 'Data agen ekspedisi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $agen = AgenEkspedisi::findOrFail($id);
        $agen->delete();

        return redirect()->route('master.data-agen-ekspedisi.index')
                         ->with('success', 'Data agen ekspedisi berhasil dihapus.');
    }

    private function generateNextId()
    {
        $maxNum = AgenEkspedisi::selectRaw('MAX(CAST(SUBSTRING(id_ekspedisi, 3) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'AE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getProvinsiByNegara($id_negara)
    {
        $provinsis = Provinsi::where('id_negara', $id_negara)
                             ->orderBy('nama_provinsi')
                             ->get(['id_provinsi', 'nama_provinsi']);
        return response()->json($provinsis);
    }

    public function getKotaByProvinsi($id_provinsi)
    {
        $kotas = Kota::where('id_provinsi', $id_provinsi)
                     ->orderBy('nama_kota')
                     ->get(['id_kota', 'nama_kota']);
        return response()->json($kotas);
    }
}