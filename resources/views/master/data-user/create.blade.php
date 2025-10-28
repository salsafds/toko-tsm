@extends('layouts.appmaster')

@section('title', 'Tambah User')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data User</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-user._form', [
      'action' => route('master.data-user.store'),
      'method' => 'POST',
      'user' => null,
      'nextId' => $nextId,
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

  const namaInput = document.querySelector('#nama_lengkap');
  const usernameInput = document.querySelector('#username_input');
  const passwordInput = document.querySelector('#password_input');
  const jenisEls = document.getElementsByName('jenis_kelamin');
  const statusSelect = document.querySelector('#status');
  const tanggalMasuk = document.querySelector('#tanggal_masuk');
  const roleSelect = document.querySelector('#id_role');
  const teleponInput = document.querySelector('#telepon');

  const namaError = document.querySelector('#nama_lengkap_error');
  const usernameError = document.querySelector('#username_error');
  const passwordError = document.querySelector('#password_error');
  const jenisError = document.querySelector('#jenis_kelamin_error');
  const statusError = document.querySelector('#status_error');
  const tanggalMasukError = document.querySelector('#tanggal_masuk_error');
  const roleError = document.querySelector('#id_role_error');
  const teleponError = document.querySelector('#telepon_error');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value;

  // helper: cek username via ajax
  async function checkUsernameAjax(username) {
    if (!username) return { exists: false };
    try {
      const res = await fetch('{{ route("master.data-user.check-username") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ username: username })
      });
      return await res.json();
    } catch (err) {
      return { exists: false };
    }
  }

  usernameInput?.addEventListener('blur', async function () {
    const val = usernameInput.value.trim();
    usernameError.textContent = '';
    usernameError.classList.add('hidden');
    usernameInput.classList.remove('border-red-500', 'bg-red-50');

    if (!val) return;

    const result = await checkUsernameAjax(val);
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

    if (!tel) return true;
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

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // reset
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

    if (username.includes(' ')) {
      usernameError.textContent = 'Username tidak boleh mengandung spasi.';
      usernameError.classList.remove('hidden');
      usernameInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

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

      const res = await checkUsernameAjax(username);
      if (res.exists) {
        usernameError.textContent = 'Username sudah digunakan.';
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
    }

    if (!password) {
      passwordError.textContent = 'Password wajib diisi.';
      passwordError.classList.remove('hidden');
      passwordInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (password.length < 6) {
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


    if (!validatePhoneClientSide()) {
      hasError = true;
    }

    if (hasError) return;

    const message = 'Apakah Anda yakin ingin menyimpan data user ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>

@endsection
