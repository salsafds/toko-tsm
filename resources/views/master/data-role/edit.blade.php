@extends('layouts.appmaster')

@section('title', 'Edit Role')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Role</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-role._form', [
      'action' => route('master.data-role.update', $role->id_role),
      'method' => 'PUT',
      'role' => $role,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#roleForm');
  const namaRoleInput = document.querySelector('#nama_role');
  const keteranganInput = document.querySelector('#keterangan');
  const submitButton = document.querySelector('#submitButton');
  const namaRoleError = document.querySelector('#nama_role_error');
  const keteranganError = document.querySelector('#keterangan_error');

  if (!form || !namaRoleInput || !keteranganInput || !submitButton || !namaRoleError || !keteranganError) return;

  // Simpan nilai awal
  const initialNamaRole = namaRoleInput.value.trim();
  const initialKeterangan = keteranganInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaRole = namaRoleInput.value.trim();
    const currentKeterangan = keteranganInput.value.trim();
    if (currentNamaRole === initialNamaRole && currentKeterangan === initialKeterangan) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input
  namaRoleInput.addEventListener('input', checkChanges);
  keteranganInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    namaRoleError.textContent = '';
    namaRoleError.classList.add('hidden');
    namaRoleInput.classList.remove('border-red-500', 'bg-red-50');
    keteranganError.textContent = '';
    keteranganError.classList.add('hidden');
    keteranganInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaRole = namaRoleInput.value.trim();
    const keterangan = keteranganInput.value.trim();

    let hasError = false;

    if (!namaRole) {
      namaRoleError.textContent = 'Nama role wajib diisi.';
      namaRoleError.classList.remove('hidden');
      namaRoleInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (namaRole.length > 100) {
      namaRoleError.textContent = 'Nama role tidak boleh lebih dari 100 karakter.';
      namaRoleError.classList.remove('hidden');
      namaRoleInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!keterangan) {
      keteranganError.textContent = 'Keterangan wajib diisi.';
      keteranganError.classList.remove('hidden');
      keteranganInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data role ini?' 
      : 'Apakah Anda yakin ingin menyimpan data role ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection