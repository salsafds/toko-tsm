@extends(Auth::check() && Auth::user()->id_role === 'R02' ? 'layouts.app-admin' : 'layouts.appmaster')

@section('title', 'Edit Profil')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Edit Profil</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="profileForm" action="{{ route('profile.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Foto Profil -->
            <div class="flex justify-center mb-6 z-0">
                <div class="relative">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 shadow-md">
                        @if($user->foto_user && \Storage::exists('public/foto_user/' . $user->foto_user))
                            <img src="{{ asset('storage/foto_user/' . $user->foto_user) }}" alt="Foto Profil" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-4xl text-gray-500">{{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <label for="foto_user" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 shadow-lg">
                        <img src="{{ asset('img/icon/iconKamera.png') }}" alt="Profile" class="h-5 w-5">
                        <input type="file" id="foto_user" name="foto_user" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-rose-600">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                    class="mt-1 w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100"
                    autocomplete="username">
                <p id="username_error" class="text-sm text-red-600 mt-1 hidden"></p>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" id="password" name="password"
                        class="mt-1 w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100"
                        placeholder="Kosongkan jika tidak ingin ganti"
                        autocomplete="new-password">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="mt-1 w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100"
                        autocomplete="new-password">
                    <p id="password_error" class="text-sm text-red-600 mt-1 hidden"></p>
                </div>
            </div>
            <!-- Password Strength Indicator -->
            <div class="mt-4 col-span-1 md:col-span-2">
                <p class="text-xs text-gray-600">Syarat password baru:</p>
                <ul id="password_requirements" class="text-xs space-y-1 mt-2">
                    <li id="req-length" class="text-red-600">✗ Minimal 8 karakter</li>
                    <li id="req-upper" class="text-red-600">✗ Harus ada huruf BESAR (A-Z)</li>
                    <li id="req-lower" class="text-red-600">✗ Harus ada huruf kecil (a-z)</li>
                    <li id="req-number" class="text-red-600">✗ Harus ada angka (0-9)</li>
                    <li id="req-symbol" class="text-red-600">✗ Harus ada simbol: - . @ # ! $ & % ^ * ( ) _ + =</li>
                </ul>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- Data Lain -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-700">Informasi Lain</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><label class="font-medium text-gray-600">ID User:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->id_user }}</span></div>
                    <div><label class="font-medium text-gray-600">Nama Lengkap:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->nama_lengkap }}</span></div>
                    <div><label class="font-medium text-gray-600">Role:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->role->nama_role ?? '-' }}</span></div>
                    <div><label class="font-medium text-gray-600">Jabatan:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->jabatan->nama_jabatan ?? '-' }}</span></div>
                    <div><label class="font-medium text-gray-600">Telepon:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->telepon ?? '-' }}</span></div>
                    <div><label class="font-medium text-gray-600">Alamat:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ $user->alamat_user ?? '-' }}</span></div>
                    <div><label class="font-medium text-gray-600">Jenis Kelamin:</label> <span class="block bg-gray-50 px-3 py-2 rounded-md">{{ ucfirst($user->jenis_kelamin) }}</span></div>
                    <div><label class="font-medium text-gray-600">Status:</label> 
                        <span class="block px-2 py-1 text-xs rounded-full {{ $user->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" id="submitBtn" disabled
                        class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Perubahan
                </button>
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Password Lama -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Password Lama</h3>
        <p class="text-sm text-gray-600 mb-4">Masukkan password lama untuk melanjutkan perubahan password.</p>
        <input type="password" id="old_password" class="w-full border rounded-md px-3 py-2 text-sm mb-1" placeholder="Password lama">
        <p id="old_password_error" class="text-xs text-red-600 mb-3 hidden">Password lama salah. Hubungi admin master jika lupa password.</p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closePasswordModal()" class="px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</button>
            <button type="button" onclick="verifyOldPassword()" class="px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800">Ubah Password</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('EDIT PROFIL SCRIPT LOADED!');

    const form = document.getElementById('profileForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const passwordError = document.getElementById('password_error'); // untuk konfirmasi tidak cocok
    const passwordStrengthError = document.getElementById('password_strength_error');
    const passwordModal = document.getElementById('passwordModal');
    const oldPasswordInput = document.getElementById('old_password');
    const oldPasswordError = document.getElementById('old_password_error');

    const requirements = {
        length: document.getElementById('req-length'),
        upper: document.getElementById('req-upper'),
        lower: document.getElementById('req-lower'),
        number: document.getElementById('req-number'),
        symbol: document.getElementById('req-symbol')
    };

    const symbolRegex = /[-.@#!$&%^()*_+=\[\]{}|\\:;"'<>,.?\/]/;

    const originalUsername = usernameInput.value.trim();

    // === VALIDASI PASSWORD STRENGTH ===
    function validatePasswordStrength(password) {
        const checks = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /\d/.test(password),
            symbol: symbolRegex.test(password)
        };

        // Update indikator
        Object.keys(checks).forEach(key => {
            if (requirements[key]) {
                const el = requirements[key];
                const passed = checks[key];
                el.textContent = el.textContent.replace(/^[✓✗]/, passed ? '✓' : '✗');
                el.className = passed ? 'text-green-600' : 'text-red-600';
            }
        });

        const allPassed = Object.values(checks).every(v => v);
        if (password) {
            if (allPassed) {
                passwordStrengthError.classList.add('hidden');
            } else {
                passwordStrengthError.classList.remove('hidden');
            }
        } else {
            passwordStrengthError.classList.add('hidden');
        }

        return allPassed;
    }

    // === CEK KONFIRMASI PASSWORD ===
    function checkPasswordMatch() {
        const pass = passwordInput.value;
        const confirm = passwordConfirm.value;

        if (pass && confirm) {
            if (pass !== confirm) {
                passwordError.textContent = 'Password tidak cocok.';
                passwordError.classList.remove('hidden');
                return false;
            } else {
                passwordError.classList.add('hidden');
                return true;
            }
        }
        passwordError.classList.add('hidden');
        return true;
    }

    // === UPDATE TOMBOL (hanya aktif jika ada perubahan) ===
    function updateSubmitButton() {
        const usernameChanged = usernameInput.value.trim() !== originalUsername && usernameInput.value.trim() !== '';
        const passwordFilled = passwordInput.value.trim() !== '';

        const hasChanges = usernameChanged || passwordFilled;
        submitBtn.disabled = !hasChanges;
    }

    // Event listeners
    usernameInput.addEventListener('input', updateSubmitButton);
    passwordInput.addEventListener('input', () => {
        validatePasswordStrength(passwordInput.value);
        checkPasswordMatch();
        updateSubmitButton();
    });
    passwordConfirm.addEventListener('input', () => {
        checkPasswordMatch();
        updateSubmitButton();
    });

    // Reset indikator saat password kosong
    passwordInput.addEventListener('input', function () {
        if (!passwordInput.value) {
            Object.values(requirements).forEach(el => {
                if (el) {
                    el.textContent = el.textContent.replace('✓', '✗');
                    el.className = 'text-red-600';
                }
            });
        }
    });

    // === SUBMIT HANDLER (logika lama tetap dipertahankan) ===
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const usernameChanged = usernameInput.value.trim() !== originalUsername && usernameInput.value.trim() !== '';
        const passwordFilled = passwordInput.value.trim() !== '';

        if (usernameChanged && passwordFilled) {
            alert('Tidak boleh mengubah username dan password sekaligus.');
            return;
        }

        if (usernameChanged) {
            if (confirm('Yakin ingin mengubah username?')) {
                form.submit();
            }
            return;
        }

        if (passwordFilled) {
            if (!checkPasswordMatch()) {
                return;
            }
            // Tidak perlu cek strength di sini → biarkan server yang validasi
            openPasswordModal();
            return;
        }

        alert('Tidak ada perubahan yang disimpan.');
    });

    // === MODAL FUNCTIONS (sama seperti sebelumnya) ===
    window.openPasswordModal = function () {
        passwordModal.classList.remove('hidden');
        oldPasswordInput.value = '';
        oldPasswordError.classList.add('hidden');
        oldPasswordInput.focus();
    };

    window.closePasswordModal = function () {
        passwordModal.classList.add('hidden');
        oldPasswordInput.value = '';
        oldPasswordError.classList.add('hidden');
    };

    window.verifyOldPassword = async function () {
        const oldPass = oldPasswordInput.value.trim();
        if (!oldPass) {
            oldPasswordError.textContent = 'Password lama wajib diisi.';
            oldPasswordError.classList.remove('hidden');
            return;
        }

        try {
            const res = await fetch('{{ route('profile.profile.verify-password') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ old_password: oldPass })
            });
            const data = await res.json();

            if (data.valid) {
                closePasswordModal();
                if (confirm('Yakin ingin mengubah password?')) {
                    form.submit();
                }
            } else {
                oldPasswordError.textContent = 'Password lama salah. Hubungi admin master jika lupa password.';
                oldPasswordError.classList.remove('hidden');
            }
        } catch (err) {
            oldPasswordError.textContent = 'Terjadi kesalahan jaringan.';
            oldPasswordError.classList.remove('hidden');
        }
    };

    // Initial
    updateSubmitButton();
    if (passwordInput.value) {
        validatePasswordStrength(passwordInput.value);
        checkPasswordMatch();
    }
});
</script>
@endsection