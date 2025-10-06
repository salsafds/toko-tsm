<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Support\Str;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query();

        if ($search = $request->query('q')) {
            $query->where('nama_satuan', 'like', "%{$search}%");
        }

        $perPage = $request->query('per_page', 10);
        $satuans = $query->paginate($perPage);

        return view('master.dataSatuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('master.dataSatuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
        ]);

        Satuan::create([
            'id_satuan' => 'ST' . Str::upper(Str::random(6)),
            'nama_satuan' => $request->nama_satuan,
        ]);

        return redirect()->route('master.dataSatuan.index')
                         ->with('success', 'Data satuan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('master.dataSatuan.show', compact('satuan'));
    }

    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('master.dataSatuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan,' . $id . ',id_satuan',
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return redirect()->route('master.dataSatuan.index')
                         ->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();

        return redirect()->route('master.dataSatuan.index')
                         ->with('success', 'Data satuan berhasil dihapus.');
    }
}
