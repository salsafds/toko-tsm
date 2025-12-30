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

  // Field yang diabaikan saat membandingkan perubahan
  const ignoreNames = new Set(['_token', '_method', 'id_user', 'password']); // password diabaikan karena opsional saat edit

  // Selector semua input yang dipantau
  const inputsSelector = 'input[name], select[name], textarea[name]';

  // Serialize form menjadi objek yang mudah dibandingkan
  function serializeForm() {
    const data = {};
    const elements = form.querySelectorAll(inputsSelector);

    elements.forEach(el => {
      const name = el.name;
      if (!name || ignoreNames.has(name)) return;

      // Radio button
      if (el.type === 'radio') {
        if (data[name] !== undefined) return;
        const checked = form.querySelector(`input[name="${name}"]:checked`);
        data[name] = checked ? checked.value : '';
        return;
      }

      // Checkbox (jika ada di masa depan)
      if (el.type === 'checkbox') {
        data[name] = form.querySelectorAll(`input[name="${name}"]:checked`).length
          ? Array.from(form.querySelectorAll(`input[name="${name}"]:checked`)).map(i => i.value)
          : [];
        return;
      }

      // Input, textarea, select biasa
      data[name] = el.value == null ? '' : String(el.value).trim();
    });

    return data;
  }

  // Bandingkan dua snapshot
  function isEqualSnapshot(a, b) {
    const aKeys = Object.keys(a).sort();
    const bKeys = Object.keys(b).sort();
    if (aKeys.length !== bKeys.length) return false;

    for (let i = 0; i < aKeys.length; i++) {
      if (aKeys[i] !== bKeys[i]) return false;
      const k = aKeys[i];
      const va = a[k];
      const vb = b[k];

      if (Array.isArray(va) || Array.isArray(vb)) {
        const arra = Array.isArray(va) ? va.slice().map(String) : [];
        const arrb = Array.isArray(vb) ? vb.slice().map(String) : [];
        if (arra.length !== arrb.length) return false;
        arra.sort(); arrb.sort();
        for (let j = 0; j < arra.length; j++) {
          if (arra[j] !== arrb[j]) return false;
        }
      } else {
        if (String(va) !== String(vb)) return false;
      }
    }
    return true;
  }

  // Snapshot nilai awal (setelah halaman loaded)
  const initialSnapshot = serializeForm();

  // Update status tombol
  function setButtonState(disabled) {
    if (!submitButton) return;
    submitButton.disabled = disabled;
    submitButton.classList.toggle('opacity-50', disabled);
    submitButton.setAttribute('aria-disabled', disabled ? 'true' : 'false');
  }

  // Cek apakah ada perubahan
  function checkChanges() {
    const current = serializeForm();
    const sama = isEqualSnapshot(initialSnapshot, current);
    setButtonState(sama);
  }

  // Pasang listener ke semua field
  form.querySelectorAll(inputsSelector).forEach(el => {
    if (!el.name || ignoreNames.has(el.name)) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  // Jalankan sekali di awal (tombol disabled jika belum ada perubahan)
  checkChanges();

  // Validasi + submit
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset error styles
    const errorEls = form.querySelectorAll('[id$="_error"]');
    errorEls.forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });

    const inputEls = form.querySelectorAll('input, select, textarea');
    inputEls.forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

    let hasError = false;

    // Ambil nilai
    const nama = form.querySelector('#nama_lengkap')?.value.trim() || '';
    const username = form.querySelector('#username_input')?.value.trim() || '';
    const telepon = form.querySelector('#telepon')?.value.trim() || '';
    const jenisKelamin = form.querySelector('input[name="jenis_kelamin"]:checked');
    const status = form.querySelector('#status')?.value || '';
    const tanggalMasuk = form.querySelector('#tanggal_masuk')?.value || '';
    const role = form.querySelector('#id_role')?.value || '';

    // Validasi wajib
    if (!nama) {
      showError('nama_lengkap_error', 'Nama lengkap wajib diisi.');
      hasError = true;
    }

    if (!username) {
      showError('username_error', 'Username wajib diisi.');
      hasError = true;
    } else if (username.includes(' ')) {
      showError('username_error', 'Username tidak boleh mengandung spasi.');
      hasError = true;
    }

    if (!jenisKelamin) {
      showError('jenis_kelamin_error', 'Jenis kelamin wajib dipilih.');
      hasError = true;
    }

    if (!status) {
      showError('status_error', 'Status wajib dipilih.');
      hasError = true;
    }

    if (!tanggalMasuk) {
      showError('tanggal_masuk_error', 'Tanggal masuk wajib diisi.');
      hasError = true;
    }

    if (!role) {
      showError('id_role_error', 'Role wajib dipilih.');
      hasError = true;
    }

    // Validasi telepon (opsional tapi jika diisi harus valid)
    if (telepon) {
      if (!/^[0-9]+$/.test(telepon)) {
        showError('telepon_error', 'Telepon harus berisi angka saja.');
        hasError = true;
      } else if (telepon.length < 10) {
        showError('telepon_error', 'Telepon minimal 10 digit.');
        hasError = true;
      } else if (telepon.length > 20) {
        showError('telepon_error', 'Telepon maksimal 20 digit.');
        hasError = true;
      }
    }

    function showError(id, message) {
      const el = document.querySelector('#' + id);
      const input = el?.closest('.grid')?.querySelector('input, select, textarea');
      if (el) {
        el.textContent = message;
        el.classList.remove('hidden');
      }
      if (input) input.classList.add('border-red-500', 'bg-red-50');
    }

    if (hasError) return;

    // Confirm sebelum update
    if (confirm('Apakah Anda yakin ingin mengupdate data user ini?')) {
      form.submit();
    }
  });
});
</script>
@endsection