@extends('layouts.appmaster')

@section('title', 'Tambah Jabatan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Jabatan</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-jabatan._form', [
      'action' => route('master.data-jabatan.store'),
      'method' => 'POST',
      'jabatan' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#jabatanForm');
  const namaJabatanInput = document.querySelector('#nama_jabatan');
  const errorMessage = document.querySelector('#nama_jabatan_error');

  if (!form || !namaJabatanInput || !errorMessage) return;

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
    if (namaJabatan.length > 100) {
      errorMessage.textContent = 'Nama jabatan tidak boleh lebih dari 100 karakter.';
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