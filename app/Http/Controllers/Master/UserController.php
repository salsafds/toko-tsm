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
        // Validasi + simpan hasilnya ke $validated
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username|regex:/^\S+$/',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'status' => 'required|in:aktif,nonaktif,cuti',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_role' => 'required|exists:role,id_role',
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan',
            'id_pendidikan' => 'nullable|exists:pendidikan,id_pendidikan',
            'telepon' => ['nullable','string','min:10','max:20','regex:/^[0-9]+$/'],
            'alamat_user' => 'nullable|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'password.required' => 'Password wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'id_role.required' => 'Role wajib dipilih.',
            'telepon.min' => 'Telepon minimal 10 karakter.',
            'telepon.max' => 'Telepon maksimal 20 karakter.',
            'telepon.regex' => 'Telepon harus berisi angka saja.',
        ]);

        // Tambahkan ID otomatis
        $validated['id_user'] = $this->generateNextId();

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Simpan ke database
        User::create($validated);

        return redirect()
            ->route('master.data-user.index')
            ->with('success', 'Data user berhasil ditambahkan.');
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
        // Cari user dulu
        $user = User::findOrFail($id);

        // Validasi
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username|regex:/^\S+$/',
            'password' => 'nullable|string|min:6',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'status' => 'required|in:aktif,nonaktif,cuti',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_role' => 'required|exists:role,id_role',
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan',
            'id_pendidikan' => 'nullable|exists:pendidikan,id_pendidikan',
            'telepon' => ['nullable','string','min:10','max:20','regex:/^[0-9]+$/'],
            'alamat_user' => 'nullable|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'id_role.required' => 'Role wajib dipilih.',
            'telepon.min' => 'Telepon minimal 10 karakter.',
            'telepon.max' => 'Telepon maksimal 20 karakter.',
            'telepon.regex' => 'Telepon harus berisi angka saja.',
        ]);

        // Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Jangan ubah password lama
        }

        // Update data
        $user->update($validated);

        return redirect()
            ->route('master.data-user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $excludeId = $request->input('id_user');

        if (!$username) {
            return response()->json(['exists' => false]);
        }

        $query = User::where('username', $username);
        if ($excludeId) {
            $query->where('id_user', '!=', $excludeId);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('master.data-user.index')
            ->with('success', 'Data user berhasil dihapus.');
    }

    private function generateNextId()
    {
        $maxNum = User::selectRaw('MAX(CAST(SUBSTRING(id_user, 4) AS UNSIGNED)) as max_num')
                      ->value('max_num') ?? 0;
        $nextNumber = $maxNum + 1;
        return 'USR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}