@extends('layouts.appmaster')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data User</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-user._form', [
      'action' => route('master.data-user.update', $user->id_user),
      'method' => 'PUT',
      'user' => $user,
      'roles' => $roles,
      'jabatans' => $jabatans,
      'pendidikans' => $pendidikans,
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#userForm');
  if (!form) return;

  const submitButton = document.querySelector('#submitButton');
  const namaInput = document.querySelector('#nama_lengkap');
  const usernameInput = document.querySelector('#username');
  const passwordInput = document.querySelector('#password');
  const jenisEls = document.getElementsByName('jenis_kelamin');
  const statusSelect = document.querySelector('#status');
  const tanggalMasuk = document.querySelector('#tanggal_masuk');
  const tanggalKeluar = document.querySelector('#tanggal_keluar');
  const roleSelect = document.querySelector('#id_role');
  const jabatanSelect = document.querySelector('#id_jabatan');
  const pendidikanSelect = document.querySelector('#id_pendidikan');
  const teleponInput = document.querySelector('#telepon');
  const alamatInput = document.querySelector('#alamat_user');

  const namaError = document.querySelector('#nama_lengkap_error');
  const usernameError = document.querySelector('#username_error');
  const passwordError = document.querySelector('#password_error');
  const jenisError = document.querySelector('#jenis_kelamin_error');
  const statusError = document.querySelector('#status_error');
  const tanggalMasukError = document.querySelector('#tanggal_masuk_error');
  const roleError = document.querySelector('#id_role_error');
  const teleponError = document.querySelector('#telepon_error');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value;
  const currentUserId = "{{ $user->id_user }}";

  async function checkUsernameAjax(username, excludeId = null) {
    if (!username) return { exists: false };
    try {
      const res = await fetch('{{ route("master.data-user.check-username") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ username: username, id_user: excludeId })
      });
      return await res.json();
    } catch (err) {
      return { exists: false };
    }
  }

  // username blur check (exclude self)
  usernameInput?.addEventListener('blur', async function () {
    const val = usernameInput.value.trim();
    usernameError.textContent = '';
    usernameError.classList.add('hidden');
    usernameInput.classList.remove('border-red-500', 'bg-red-50');

    if (!val) return;

    const result = await checkUsernameAjax(val, currentUserId);
    if (result.exists) {
      usernameError.textContent = 'Username sudah digunakan.';
      usernameError.classList.remove('hidden');
      usernameInput.classList.add('border-red-500', 'bg-red-50');
    }
  });

  function validatePhoneClientSide() {
    const tel = teleponInput?.value.trim() || '';
    teleponError.textContent = '';
    teleponError.classList.add('hidden');
    teleponInput.classList.remove('border-red-500', 'bg-red-50');

    if (!tel) return true; // optional
    if (!/^[0-9]+$/.test(tel)) {
      teleponError.textContent = 'Telepon harus berisi angka saja.';
      teleponError.classList.remove('hidden');
      teleponInput.classList.add('border-red-500', 'bg-red-50');
      return false;
    }
    if (tel.length < 10) {
      teleponError.textContent = 'Telepon minimal 10 karakter.';
      teleponError.classList.remove('hidden');
      teleponInput.classList.add('border-red-500', 'bg-red-50');
      return false;
    }
    if (tel.length > 20) {
      teleponError.textContent = 'Telepon maksimal 20 karakter.';
      teleponError.classList.remove('hidden');
      teleponInput.classList.add('border-red-500', 'bg-red-50');
      return false;
    }
    return true;
  }

  // ... keep the existing change-detection logic (initial/checkChanges) above unchanged
  // (omit here for brevity) — but keep it exactly as Anda sudah miliki.
  // Ensure we still call checkChanges() on load.

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // reset errors
    [namaError, usernameError, passwordError, jenisError, statusError, tanggalMasukError, roleError, teleponError].forEach(el => {
      if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    [namaInput, usernameInput, passwordInput, statusSelect, tanggalMasuk, roleSelect, teleponInput].forEach(el => {
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;
    const nama = namaInput.value.trim();
    const username = usernameInput.value.trim();
    const password = passwordInput.value;
    const jenis = Array.from(jenisEls).find(r => r.checked)?.value || '';
    const status = statusSelect.value;
    const masuk = tanggalMasuk.value;
    const role = roleSelect.value;

    if (!nama) {
      namaError.textContent = 'Nama lengkap wajib diisi.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!username) {
      usernameError.textContent = 'Username wajib diisi.';
      usernameError.classList.remove('hidden');
      usernameInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else {
      // check username excluding current user
      const res = await checkUsernameAjax(username, currentUserId);
      if (res.exists) {
        usernameError.textContent = 'Username sudah digunakan.';
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
    }

    if (password && password.length < 6) {
      passwordError.textContent = 'Password minimal 6 karakter.';
      passwordError.classList.remove('hidden');
      passwordInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!jenis) {
      jenisError.textContent = 'Jenis kelamin wajib dipilih.';
      jenisError.classList.remove('hidden');
      hasError = true;
    }

    if (!status) {
      statusError.textContent = 'Status wajib dipilih.';
      statusError.classList.remove('hidden');
      statusSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!masuk) {
      tanggalMasukError.textContent = 'Tanggal masuk wajib diisi.';
      tanggalMasukError.classList.remove('hidden');
      tanggalMasuk.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!role) {
      roleError.textContent = 'Role wajib dipilih.';
      roleError.classList.remove('hidden');
      roleSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    // telepon client-side validation
    if (!validatePhoneClientSide()) {
      hasError = true;
    }

    if (hasError) return;

    const message = 'Apakah Anda yakin ingin mengedit data user ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>

@endsection
