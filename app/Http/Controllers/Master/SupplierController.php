<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Kota;

class SupplierController extends Controller
{
    /**
     * Index: list supplier dengan search dan per_page
     */
    public function index(Request $request)
    {
        $query = Supplier::with(['negara', 'provinsi', 'kota']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('id_supplier', 'like', "%{$search}%")
                  ->orWhere('email_supplier', 'like', "%{$search}%")
                  ->orWhere('telepon_supplier', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $suppliers = $query->orderBy('id_supplier', 'asc')->paginate($perPage);

        return view('master.data-supplier.index', compact('suppliers'));
    }

    /**
     * Form create (dengan next ID dan dropdown)
     */
    public function create()
    {
        $nextId = $this->generateNextId();
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = collect([]); // Empty, populated via AJAX
        $kota = collect([]); // Empty, populated via AJAX

        return view('master.data-supplier.create', compact('nextId', 'negara', 'provinsi', 'kota'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100|unique:supplier,nama_supplier',
            'alamat' => 'required|string',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_supplier' => 'required|string|min:10|max:20',
            'email_supplier' => 'nullable|email|max:100|unique:supplier,email_supplier',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.max' => 'Nama supplier tidak boleh lebih dari 100 karakter.',
            'nama_supplier.unique' => 'Nama supplier sudah digunakan.',
            'alamat.required' => 'Alamat wajib diisi.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'telepon_supplier.required' => 'Telepon wajib diisi.',
            'telepon_supplier.min' => 'Telepon minimal 10 karakter.',  
            'telepon_supplier.max' => 'Telepon maksimal 20 karakter.',
            'email_supplier.email' => 'Format email tidak valid.',
            'email_supplier.max' => 'Email tidak boleh lebih dari 100 karakter.',
            'email_supplier.unique' => 'Email sudah digunakan.',
        ]);

        Supplier::create([
            'id_supplier' => $this->generateNextId(),
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'telepon_supplier' => $request->telepon_supplier,
            'email_supplier' => $request->email_supplier,
        ]);

        return redirect()->route('master.data-supplier.index')
                         ->with('success', 'Data supplier berhasil ditambahkan.');
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $negara = Negara::orderBy('nama_negara')->get();
        $provinsi = Provinsi::where('id_negara', $supplier->id_negara)->orderBy('nama_provinsi')->get();
        $kota = Kota::where('id_provinsi', $supplier->id_provinsi)->orderBy('nama_kota')->get();

        return view('master.data-supplier.edit', compact('supplier', 'negara', 'provinsi', 'kota'));
    }

    /**
     * Update data supplier
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100|unique:supplier,nama_supplier,' . $id . ',id_supplier',
            'alamat' => 'required|string',
            'id_negara' => 'required|exists:negara,id_negara',
            'id_provinsi' => 'required|exists:provinsi,id_provinsi',
            'id_kota' => 'required|exists:kota,id_kota',
            'telepon_supplier' => 'required|string|min:10|max:20',
            'email_supplier' => 'nullable|email|max:100|unique:supplier,email_supplier,' . $id . ',id_supplier',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.max' => 'Nama supplier tidak boleh lebih dari 100 karakter.',
            'nama_supplier.unique' => 'Nama supplier sudah digunakan.',
            'alamat.required' => 'Alamat wajib diisi.',
            'id_negara.required' => 'Negara wajib dipilih.',
            'id_negara.exists' => 'Negara yang dipilih tidak valid.',
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_provinsi.exists' => 'Provinsi yang dipilih tidak valid.',
            'id_kota.required' => 'Kota wajib dipilih.',
            'id_kota.exists' => 'Kota yang dipilih tidak valid.',
            'telepon_supplier.required' => 'Telepon wajib diisi.',
            'telepon_supplier.min' => 'Telepon minimal 10 karakter.',
            'telepon_supplier.max' => 'Telepon maksimal 20 karakter.', 
            'email_supplier.email' => 'Format email tidak valid.',
            'email_supplier.max' => 'Email tidak boleh lebih dari 100 karakter.',
            'email_supplier.unique' => 'Email sudah digunakan.',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->only([
            'nama_supplier',
            'alamat',
            'id_negara',
            'id_provinsi',
            'id_kota',
            'telepon_supplier',
            'email_supplier',
        ]));

        return redirect()->route('master.data-supplier.index')
                         ->with('success', 'Data supplier berhasil diperbarui.');
    }

    /**
     * Hapus data supplier
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('master.data-supplier.index')
                         ->with('success', 'Data supplier berhasil dihapus.');
    }

    /**
     * Generate next ID format SP001, SP002, ...
     */
    private function generateNextId()
    {
        $maxNum = Supplier::selectRaw('MAX(CAST(SUBSTRING(id_supplier, 3) AS UNSIGNED)) as max_num')
                          ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'SP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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