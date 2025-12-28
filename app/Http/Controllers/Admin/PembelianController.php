<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with(['supplier', 'user', 'detailPembelian'])
            ->where('id_user', Auth::user()->id_user)
            ->orderBy('id_pembelian', 'desc');
        if ($periode = $request->query('periode')) {
        switch ($periode) {
            case '7days':
                $query->where('tanggal_pembelian', '>=', now()->subDays(7));
                break;
            case '3months':
                $query->where('tanggal_pembelian', '>=', now()->subMonths(3));
                break;
            case '1year':
                $query->where('tanggal_pembelian', '>=', now()->subYears(1));
                break;
            case 'all':
            default:
                break;
        }}
            
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id_pembelian', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('nama_supplier', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%");
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
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all();
        $satuan = Satuan::all();
        $nextId = $this->generateNextId();
        $nextIdBarang = $this->generateNextIdBarang();

        return view('admin.pembelian.create', compact('suppliers', 'barangs', 'kategoriBarang', 'satuan', 'nextId', 'nextIdBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|string|unique:pembelian,id_pembelian',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'jenis_pembayaran' => 'required|in:Cash,Kredit',
            'diskon' => 'nullable|numeric|min:0|max:100', 
            'ppn' => 'nullable|numeric|min:0|max:100', 
            'biaya_pengiriman' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0',
            'details.*.kuantitas' => 'required|integer|min:1',
        ]);

        $totalSubTotal = 0;
        foreach ($request->details as $detail) {
            $totalSubTotal += $detail['harga_beli'] * $detail['kuantitas'];
        }

        $nilaiDiskon = ($request->diskon / 100) * $totalSubTotal;
        $setelahDiskon = $totalSubTotal - $nilaiDiskon;

        $nilaiPpn = ($request->ppn / 100) * $setelahDiskon;
        $totalSetelahPpn = $setelahDiskon + $nilaiPpn;

        $jumlahBayar = $totalSetelahPpn + $request->biaya_pengiriman;

        if ($jumlahBayar < 0) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar tidak boleh kurang dari 0.'])->withInput();
        }

        $pembelian = Pembelian::create([
            'id_pembelian' => $request->id_pembelian,
            'tanggal_pembelian' => now(),
            'id_supplier' => $request->id_supplier,
            'id_user' => Auth::user()->id_user,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'diskon' => $request->diskon,
            'ppn' => $request->ppn,
            'biaya_pengiriman' => $request->biaya_pengiriman,
            'jumlah_bayar' => $jumlahBayar,
            'catatan' => $request->catatan,
        ]);

        foreach ($request->details as $detail) {
            DetailPembelian::create([
                'id_detail_pembelian' => $this->generateNextDetailId(),
                'id_pembelian' => $pembelian->id_pembelian,
                'id_barang' => $detail['id_barang'],
                'harga_beli' => $detail['harga_beli'],
                'kuantitas' => $detail['kuantitas'],
                'sub_total' => $detail['harga_beli'] * $detail['kuantitas'],
            ]);
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil ditambahkan.');
    }

    public function edit($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diedit.');
        }

        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $kategoriBarang = KategoriBarang::all();
        $satuan = Satuan::all();
        $nextIdBarang = $this->generateNextIdBarang();

        return view('admin.pembelian.edit', compact('pembelian', 'suppliers', 'barangs', 'kategoriBarang', 'satuan', 'nextIdBarang'));
    }

    public function update(Request $request, $id_pembelian)
    {
        $request->validate([
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'jenis_pembayaran' => 'required|in:Cash,Kredit',
            'diskon' => 'nullable|numeric|min:0|max:100',  
            'ppn' => 'nullable|numeric|min:0|max:100', 
            'biaya_pengiriman' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.harga_beli' => 'required|numeric|min:0',
            'details.*.kuantitas' => 'required|integer|min:1',
            'details.*.id_detail_pembelian' => 'nullable|exists:detail_pembelian,id_detail_pembelian',
        ]);

        $pembelian = Pembelian::findOrFail($id_pembelian);
        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat diupdate.');
        }

        $totalSubTotal = 0;
        foreach ($request->details as $detail) {
            $totalSubTotal += $detail['harga_beli'] * $detail['kuantitas'];
        }

        $nilaiDiskon = ($request->diskon / 100) * $totalSubTotal;
        $setelahDiskon = $totalSubTotal - $nilaiDiskon;

        $nilaiPpn = ($request->ppn / 100) * $setelahDiskon;
        $totalSetelahPpn = $setelahDiskon + $nilaiPpn;

        $jumlahBayar = $totalSetelahPpn + $request->biaya_pengiriman;

        if ($jumlahBayar < 0) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar tidak boleh kurang dari 0.'])->withInput();
        }

        $pembelian->update([
            'id_supplier' => $request->id_supplier,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'diskon' => $request->diskon,
            'ppn' => $request->ppn,
            'biaya_pengiriman' => $request->biaya_pengiriman,
            'jumlah_bayar' => $jumlahBayar,
            'catatan' => $request->catatan,
        ]);

        $existingDetailIds = $pembelian->detailPembelian->pluck('id_detail_pembelian')->toArray();
        $submittedDetailIds = array_filter(array_column($request->details ?? [], 'id_detail_pembelian'));

        DetailPembelian::where('id_pembelian', $pembelian->id_pembelian)
            ->whereNotIn('id_detail_pembelian', $submittedDetailIds)
            ->delete();

        foreach ($request->details as $detail) {
            $detailData = [
                'id_pembelian' => $pembelian->id_pembelian,
                'id_barang' => $detail['id_barang'],
                'harga_beli' => $detail['harga_beli'],
                'kuantitas' => $detail['kuantitas'],
                'sub_total' => $detail['harga_beli'] * $detail['kuantitas'],
            ];

            if (!empty($detail['id_detail_pembelian'])) {
                DetailPembelian::where('id_detail_pembelian', $detail['id_detail_pembelian'])
                    ->update($detailData);
            } else {
                $detailData['id_detail_pembelian'] = $this->generateNextDetailId();
                DetailPembelian::create($detailData);
            }
        }

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil diperbarui.');
    }

    public function destroy($id_pembelian)
    {
        $pembelian = Pembelian::findOrFail($id_pembelian);

        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')->with('error', 'Pembelian sudah selesai, tidak dapat dihapus.');
        }

        $pembelian->detailPembelian()->delete();
        $pembelian->delete();

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pembelian berhasil dihapus.');
    }

    public function selesai($id_pembelian)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id_pembelian);

        if ($pembelian->tanggal_terima) {
            return redirect()->route('admin.pembelian.index')
                ->with('error', 'Pembelian sudah selesai.');
        }

        $totalSebelumDiskon = $pembelian->detailPembelian->sum('sub_total');
        $nilaiDiskon = $totalSebelumDiskon * ($pembelian->diskon / 100);
        $totalSetelahDiskon = $totalSebelumDiskon - $nilaiDiskon;
        $biayaTambahan = $pembelian->biaya_pengiriman ?? 0;
        $totalNilaiPersediaan = $totalSetelahDiskon + $biayaTambahan;
            
        $biayaTambahan = $pembelian->biaya_pengiriman ?? 0;

        $pembelian->update(['tanggal_terima' => now()]);

        foreach ($pembelian->detailPembelian as $detail) {
            $proporsi = $totalSebelumDiskon > 0 ? $detail->sub_total / $totalSebelumDiskon : 0;

            $diskonDialokasikan = $nilaiDiskon * $proporsi;
            $biayaTambahanDialokasikan = $biayaTambahan * $proporsi;

            $hppPerUnit = $detail->kuantitas > 0
                ? ($detail->sub_total - $diskonDialokasikan + $biayaTambahanDialokasikan) / $detail->kuantitas
                : 0;

            app(BarangController::class)->tambahStokDariPembelian(
                $detail->id_barang,
                $detail->kuantitas,
                $hppPerUnit
            );
        }

        return redirect()->route('admin.pembelian.index')
            ->with('success', 'Pembelian selesai! Stok & HPP diperbarui');
    }
 
    private function generateNextId()
    {
        $maxNum = Pembelian::selectRaw('MAX(CAST(SUBSTRING(id_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'PB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateNextDetailId()
    {
        $maxNum = DetailPembelian::selectRaw('MAX(CAST(SUBSTRING(id_detail_pembelian, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'DTL' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateNextIdBarang()
    {
        $maxNum = Barang::selectRaw('MAX(CAST(SUBSTRING(id_barang, 4) AS UNSIGNED)) as max_num')->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'BRG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function storeBarang(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'sku' => 'required|string|unique:barang,sku', 
            'id_kategori_barang' => 'required|exists:kategori_barang,id_kategori_barang',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'merk_barang' => 'nullable|string|max:255',
            'berat' => 'required|numeric|min:0.01',
            'margin' => 'nullable|numeric|min:0|max:100',
            'kena_ppn' => 'required|in:Ya,Tidak', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() 
            ], 422);
        }

        try {
            $barang = Barang::create([
                'id_barang' => $this->generateNextIdBarang(),
                'nama_barang' => $request->nama_barang,
                'sku' => $request->sku, 
                'id_kategori_barang' => $request->id_kategori_barang,
                'id_supplier' => $request->id_supplier,
                'id_satuan' => $request->id_satuan,
                'merk_barang' => $request->merk_barang ?? '-',
                'berat' => $request->berat,
                'kena_ppn' => $request->kena_ppn, 
                'harga_beli' => 0,
                'stok' => 0,
                'retail' => 0,
                'margin' => $request->margin ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang baru berhasil ditambahkan.',
                'barang' => $barang
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['supplier', 'user', 'detailPembelian.barang']) 
            ->findOrFail($id);
        return view('admin.pembelian.show', compact('pembelian'));
    }
}