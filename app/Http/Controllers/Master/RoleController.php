<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_role', 'like', "%{$search}%")
                  ->orWhere('id_role', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $roles = $query->orderBy('id_role', 'asc')->paginate($perPage);

        return view('master.data-role.index', compact('roles'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        return view('master.data-role.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:100|unique:role,nama_role',
            'keterangan' => 'required|string',
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
            'nama_role.string' => 'Nama role harus berupa teks.',
            'nama_role.max' => 'Nama role tidak boleh lebih dari 100 karakter.',
            'nama_role.unique' => 'Nama role sudah digunakan.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
        ]);

        Role::create([
            'id_role' => $this->generateNextId(),
            'nama_role' => $request->nama_role,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('master.data-role.index')
                         ->with('success', 'Data role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('master.data-role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_role' => 'required|string|max:100|unique:role,nama_role,' . $id . ',id_role',
            'keterangan' => 'required|string',
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
            'nama_role.string' => 'Nama role harus berupa teks.',
            'nama_role.max' => 'Nama role tidak boleh lebih dari 100 karakter.',
            'nama_role.unique' => 'Nama role sudah digunakan.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'nama_role' => $request->nama_role,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('master.data-role.index')
                         ->with('success', 'Data role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('master.data-role.index')
                         ->with('success', 'Data role berhasil dihapus.');
    }

    //GENERATE ID ROLE
    private function generateNextId()
    {
        $maxNum = Role::selectRaw('MAX(CAST(SUBSTRING(id_role, 3) AS UNSIGNED)) as max_num')
                      ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;

        return 'RL' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}