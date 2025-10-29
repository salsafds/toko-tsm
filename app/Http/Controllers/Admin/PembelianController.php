<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\User; // Asumsikan model User
use App\Models\Barang;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query()->with(['supplier', 'user', 'detailPembelian']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_pembelian', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('nama_supplier', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%"); // Asumsikan kolom name di User
                  });
            });
        }

        $perPage = $request->query('per_page', 10);
        $pembelian = $query->paginate($perPage);

        return view('admin.pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $users = User::all(); // Asumsikan model User
        $barangs = Barang::all();
        $nextId = $this->generateNextId();

        return view('admin.pembelian.create', compact('suppliers', 'users', 'barangs', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|string|unique:pembelian,id_pembelian',
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_user' => 'required|exists:users,id', // Asumsikan table users
            'jenis_pembayaran' => 'required|in:cash,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.kuantitas' => 'required|integer|min:1',
        ]);

        $pembelian = Pembelian::create([
            'id_pembelian' => $request->id_pembelian,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'id_supplier' => $request->id_supplier,
            'id_user' => $request->id_user,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'jumlah_bayar' => $request->jumlah_bayar,
        ]);

        if ($request->details) {
            foreach ($request->details as $detail) {
                $barang = Barang::find($detail['id_barang']);
                DetailPembelian::create([
                    'id_detail_pembelian' => $this->generateNextDetailId(),
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'sub_total' => $barang->harga_beli * $detail['kuantitas'],
                    'kuantitas' => $detail['kuantitas'],
                ]);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function edit($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);
        $suppliers = Supplier::all();
        $users = User::all();
        $barangs = Barang::all();

        return view('admin.pembelian.edit', compact('pembelian', 'suppliers', 'users', 'barangs'));
    }

    public function update(Request $request, $id_pembelian)
    {
        $request->validate([
            'tanggal_pembelian' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_user' => 'required|exists:users,id',
            'jenis_pembayaran' => 'required|in:cash,kredit',
            'jumlah_bayar' => 'required|numeric|min:0',
            'details' => 'nullable|array',
        ]);

        $pembelian = Pembelian::findOrFail($id_pembelian);
        $pembelian->update($request->only(['tanggal_pembelian', 'id_supplier', 'id_user', 'jenis_pembayaran', 'jumlah_bayar']));

        // Update details (hapus lama, tambah baru)
        $pembelian->detailPembelian()->delete();
        if ($request->details) {
            foreach ($request->details as $detail) {
                $barang = Barang::find($detail['id_barang']);
                DetailPembelian::create([
                    'id_detail_pembelian' => $this->generateNextDetailId(),
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $detail['id_barang'],
                    'sub_total' => $barang->harga_beli * $detail['kuantitas'],
                    'kuantitas' => $detail['kuantitas'],
                ]);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy($id_pembelian)
    {
        $pembelian = Pembelian::findOrFail($id_pembelian);
        $pembelian->detailPembelian()->delete();
        $pembelian->delete();

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
    }

    public function selesai($id_pembelian)
    {
        $pembelian = Pembelian::findOrFail($id_pembelian);
        $pembelian->update(['tanggal_terima' => now()]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Pembelian ditandai selesai.');
    }

    private function generateNextId()
    {
        $maxNum = Pembelian::selectRaw('MAX(CAST(SUBSTRING(id_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'PEM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateNextDetailId()
    {
        $maxNum = DetailPembelian::selectRaw('MAX(CAST(SUBSTRING(id_detail_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'DET' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
