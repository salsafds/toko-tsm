<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('id_supplier', 'like', "%{$search}%")
                  ->orWhere('telepon_supplier', 'like', "%{$search}%")
                  ->orWhere('email_supplier', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $suppliers = $query->orderBy('id_supplier', 'asc')->paginate($perPage);

        return view('master.data-supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        return view('master.data-supplier.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:191',
            'alamat' => 'required|string',
            'id_negara' => 'nullable|string|max:50',
            'id_provinsi' => 'nullable|string|max:50',
            'id_kota' => 'nullable|string|max:50',
            'telepon_supplier' => 'nullable|string|max:50',
            'email_supplier' => 'nullable|email|max:191',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email_supplier.email' => 'Format email tidak valid.',
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

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('master.data-supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:191',
            'alamat' => 'required|string',
            'id_negara' => 'nullable|string|max:50',
            'id_provinsi' => 'nullable|string|max:50',
            'id_kota' => 'nullable|string|max:50',
            'telepon_supplier' => 'nullable|string|max:50',
            'email_supplier' => 'nullable|email|max:191',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email_supplier.email' => 'Format email tidak valid.',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'id_negara' => $request->id_negara,
            'id_provinsi' => $request->id_provinsi,
            'id_kota' => $request->id_kota,
            'telepon_supplier' => $request->telepon_supplier,
            'email_supplier' => $request->email_supplier,
        ]);

        return redirect()->route('master.data-supplier.index')
                         ->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('master.data-supplier.index')
                         ->with('success', 'Data supplier berhasil dihapus.');
    }

    /**
     * Generate next ID in format SP0001, SP0002, ...
     */
    private function generateNextId()
    {
        $maxNum = Supplier::selectRaw('MAX(CAST(SUBSTRING(id_supplier, 3) AS UNSIGNED)) as max_num')
                          ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'SP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
