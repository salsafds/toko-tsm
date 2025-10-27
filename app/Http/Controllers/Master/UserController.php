<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Jabatan;
use App\Models\Pendidikan;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'jabatan', 'pendidikan']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('id_user', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $users = $query->orderBy('id_user', 'asc')->paginate($perPage);

        return view('master.data-user.index', compact('users'));
    }

    public function create()
    {
        $nextId = $this->generateNextId();
        $roles = Role::orderBy('nama_role')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $pendidikans = Pendidikan::orderBy('tingkat_pendidikan')->get();

        return view('master.data-user.create', compact('nextId', 'roles', 'jabatans', 'pendidikans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'status' => 'required|in:aktif,nonaktif,cuti',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_role' => 'required|exists:role,id_role',
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan',
            'id_pendidikan' => 'nullable|exists:pendidikan,id_pendidikan',
            'telepon' => 'nullable|string|max:16',
            'alamat_user' => 'nullable|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'id_role.required' => 'Role wajib dipilih.',
        ]);

        User::create([
            'id_user' => $this->generateNextId(),
            'nama_lengkap' => $request->nama_lengkap,
            'alamat_user' => $request->alamat_user,
            'telepon' => $request->telepon,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'status' => $request->status,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
            'id_role' => $request->id_role,
            'id_jabatan' => $request->id_jabatan,
            'id_pendidikan' => $request->id_pendidikan,
            // foto_user intentionally omitted
        ]);

        return redirect()->route('master.data-user.index')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('nama_role')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $pendidikans = Pendidikan::orderBy('tingkat_pendidikan')->get();

        return view('master.data-user.edit', compact('user', 'roles', 'jabatans', 'pendidikans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $id . ',id_user',
            'password' => 'nullable|string|min:6',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'status' => 'required|in:aktif,nonaktif,cuti',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_role' => 'required|exists:role,id_role',
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan',
            'id_pendidikan' => 'nullable|exists:pendidikan,id_pendidikan',
            'telepon' => 'nullable|string|max:16',
            'alamat_user' => 'nullable|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'id_role.required' => 'Role wajib dipilih.',
        ]);

        $user = User::findOrFail($id);

        $data = $request->only([
            'nama_lengkap',
            'alamat_user',
            'telepon',
            'username',
            'jenis_kelamin',
            'status',
            'tanggal_masuk',
            'tanggal_keluar',
            'id_role',
            'id_jabatan',
            'id_pendidikan',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('master.data-user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('master.data-user.index')->with('success', 'Data user berhasil dihapus.');
    }

    private function generateNextId()
    {
        $maxNum = User::selectRaw('MAX(CAST(SUBSTRING(id_user, 4) AS UNSIGNED)) as max_num')
                      ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'USR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
