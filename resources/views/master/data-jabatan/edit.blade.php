@extends('layouts.appmaster')

@section('title', 'Edit Jabatan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Jabatan</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-jabatan._form', [
      'action' => route('master.data-jabatan.update', $jabatan->id_jabatan),
      'method' => 'PUT',
      'jabatan' => $jabatan,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#jabatanForm');
  const namaJabatanInput = document.querySelector('#nama_jabatan');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_jabatan_error');

  if (!form || !namaJabatanInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_jabatan
  const initialNamaJabatan = namaJabatanInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaJabatan = namaJabatanInput.value.trim();
    if (currentNamaJabatan === initialNamaJabatan) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_jabatan
  namaJabatanInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaJabatanInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaJabatan = namaJabatanInput.value.trim();
    if (!namaJabatan) {
      errorMessage.textContent = 'Nama jabatan wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaJabatanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaJabatan.length > 50) {
      errorMessage.textContent = 'Nama jabatan tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      namaJabatanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data jabatan ini?' 
      : 'Apakah Anda yakin ingin menyimpan data jabatan ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection