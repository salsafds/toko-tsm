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

  const namaError = document.querySelector('#nama_lengkap_error');
  const usernameError = document.querySelector('#username_error');
  const passwordError = document.querySelector('#password_error');
  const jenisError = document.querySelector('#jenis_kelamin_error');
  const statusError = document.querySelector('#status_error');
  const tanggalMasukError = document.querySelector('#tanggal_masuk_error');
  const roleError = document.querySelector('#id_role_error');

  // initial values to detect changes
  const initial = {
    nama: namaInput?.value.trim() || '',
    username: usernameInput?.value.trim() || '',
    jenis: Array.from(jenisEls).find(r => r.checked)?.value || '',
    status: statusSelect?.value || '',
    masuk: tanggalMasuk?.value || '',
    keluar: tanggalKeluar?.value || '',
    role: roleSelect?.value || '',
    telepon: document.querySelector('#telepon')?.value || '',
    alamat: document.querySelector('#alamat_user')?.value || ''
  };

  function checkChanges() {
    const current = {
      nama: namaInput?.value.trim() || '',
      username: usernameInput?.value.trim() || '',
      jenis: Array.from(jenisEls).find(r => r.checked)?.value || '',
      status: statusSelect?.value || '',
      masuk: tanggalMasuk?.value || '',
      keluar: tanggalKeluar?.value || '',
      role: roleSelect?.value || '',
      telepon: document.querySelector('#telepon')?.value || '',
      alamat: document.querySelector('#alamat_user')?.value || ''
    };
    const same = Object.keys(initial).every(k => initial[k] === current[k]);
    submitButton.disabled = same;
    submitButton.classList.toggle('opacity-50', same);
  }

  [namaInput, usernameInput, passwordInput, statusSelect, tanggalMasuk, tanggalKeluar, roleSelect, document.querySelector('#telepon'), document.querySelector('#alamat_user')].forEach(el => {
    if (!el) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });
  Array.from(jenisEls).forEach(r => r.addEventListener('change', checkChanges));

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // reset errors
    [namaError, usernameError, passwordError, jenisError, statusError, tanggalMasukError, roleError].forEach(el => {
      if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    [namaInput, usernameInput, passwordInput, statusSelect, tanggalMasuk, roleSelect].forEach(el => {
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
    }

    // password optional on edit - validate min length if provided
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

    if (hasError) return;

    const message = 'Apakah Anda yakin ingin mengedit data user ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>
@endsection
