<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Kota;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query()->with(['kota', 'provinsi', 'negara']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('id_pelanggan', 'like', "%{$search}%")
                  ->orWhere('email_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $pelanggans = $query->paginate($perPage);

        return view('master.data-pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = collect([]); // Empty, populated via AJAX
        $kota = collect([]); // Empty, populated via AJAX
        $kategoriList = [
            'badan_usaha' => 'Badan Usaha',
            'perorangan' => 'Perorangan',
            'pelanggan_umum' => 'Pelanggan Umum',
        ];

        return view('master.data-pelanggan.create', compact('nextId', 'negara', 'provinsi', 'kota', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|min:10|max:20', 
            'kategori_pelanggan' => 'required|in:badan_usaha,perorangan,pelanggan_umum',
            'email_pelanggan' => 'nullable|email|max:100|unique:pelanggan,email_pelanggan',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'alamat_pelanggan' => 'required|string|max:255',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'nama_pelanggan.string' => 'Nama pelanggan harus berupa teks.',
            'nama_pelanggan.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.min' => 'Nomor telepon minimal 10 karakter.',
            'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'kategori_pelanggan.required' => 'Kategori pelanggan wajib dipilih.',
            'kategori_pelanggan.in' => 'Kategori pelanggan tidak valid.',
            'email_pelanggan.email' => 'Email tidak valid.',
            'email_pelanggan.max' => 'Email tidak boleh lebih dari 100 karakter.',
            'email_pelanggan.unique' => 'Email sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'alamat_pelanggan.required' => 'Alamat wajib diisi.',
            'alamat_pelanggan.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
        ]);

        Pelanggan::create([
            'id_pelanggan' => $this->generateNextId(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'nomor_telepon' => $request->nomor_telepon,
            'kategori_pelanggan' => $request->kategori_pelanggan,
            'email_pelanggan' => $request->email_pelanggan,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'alamat_pelanggan' => $request->alamat_pelanggan,
        ]);

        return redirect()->route('master.data-pelanggan.index')
                        ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = Provinsi::where('id_negara', $pelanggan->id_negara)->orderBy('nama_provinsi')->get();
        $kota = Kota::where('id_provinsi', $pelanggan->id_provinsi)->orderBy('nama_kota')->get();
        $kategoriList = [
            'badan_usaha' => 'Badan Usaha',
            'perorangan' => 'Perorangan',
            'pelanggan_umum' => 'Pelanggan Umum',
        ];

        return view('master.data-pelanggan.edit', compact('pelanggan', 'negara', 'provinsi', 'kota', 'kategoriList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|min:10|max:20', 
            'kategori_pelanggan' => 'required|in:badan_usaha,perorangan,pelanggan_umum',
            'email_pelanggan' => 'nullable|email|max:100|unique:pelanggan,email_pelanggan,' . $id . ',id_pelanggan',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'alamat_pelanggan' => 'required|string|max:255',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'nama_pelanggan.string' => 'Nama pelanggan harus berupa teks.',
            'nama_pelanggan.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.min' => 'Nomor telepon minimal 10 karakter.',
            'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'kategori_pelanggan.required' => 'Kategori pelanggan wajib dipilih.',
            'kategori_pelanggan.in' => 'Kategori pelanggan tidak valid.',
            'email_pelanggan.email' => 'Email tidak valid.',
            'email_pelanggan.max' => 'Email tidak boleh lebih dari 100 karakter.',
            'email_pelanggan.unique' => 'Email sudah digunakan.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'alamat_pelanggan.required' => 'Alamat wajib diisi.',
            'alamat_pelanggan.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'nomor_telepon' => $request->nomor_telepon,
            'kategori_pelanggan' => $request->kategori_pelanggan,
            'email_pelanggan' => $request->email_pelanggan,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'alamat_pelanggan' => $request->alamat_pelanggan,
        ]);

        return redirect()->route('master.data-pelanggan.index')
                        ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('master.data-pelanggan.index')
                        ->with('success', 'Data pelanggan berhasil dihapus.');
    }

    private function generateNextId()
    {
        $maxNum = Pelanggan::selectRaw('MAX(CAST(SUBSTRING(id_pelanggan, 4) AS UNSIGNED)) as max_num')
                           ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'PLG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Fetch provinsi by negara
     */
    public function getProvinsiByNegara($id_negara)
    {
        $provinsis = Provinsi::where('id_negara', $id_negara)
                             ->orderBy('nama_provinsi')
                             ->get(['id_provinsi', 'nama_provinsi']);
        return response()->json($provinsis);
    }

    /**
     * Fetch kota by provinsi
     */
    public function getKotaByProvinsi($id_provinsi)
    {
        $kotas = Kota::where('id_provinsi', $id_provinsi)
                     ->orderBy('nama_kota')
                     ->get(['id_kota', 'nama_kota']);
        return response()->json($kotas);
    }
}