@extends('layouts.appmaster')

@section('title', 'Tambah Provinsi')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Provinsi</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-provinsi._form', [
      'action' => route('master.data-provinsi.store'),
      'method' => 'POST',
      'provinsi' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#provinsiForm');
  const namaProvinsiInput = document.querySelector('#nama_provinsi');
  const errorMessage = document.querySelector('#nama_provinsi_error');

  if (!form || !namaProvinsiInput || !errorMessage) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaProvinsiInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaProvinsi = namaProvinsiInput.value.trim();
    if (!namaProvinsi) {
      errorMessage.textContent = 'Nama provinsi wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaProvinsiInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaProvinsi.length > 100) {
      errorMessage.textContent = 'Nama provinsi tidak boleh lebih dari 100 karakter.';
      errorMessage.classList.remove('hidden');
      namaProvinsiInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data provinsi ini?' 
      : 'Apakah Anda yakin ingin menyimpan data provinsi ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection