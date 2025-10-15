@extends('layouts.appmaster')

@section('title', 'Tambah Bahasa')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Bahasa</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-bahasa._form', [
      'action' => route('master.data-bahasa.store'),
      'method' => 'POST',
      'bahasa' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#bahasaForm');
  const namaBahasaInput = document.querySelector('#nama_bahasa');
  const errorMessage = document.querySelector('#nama_bahasa_error');

  if (!form || !namaBahasaInput || !errorMessage) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaBahasaInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaBahasa = namaBahasaInput.value.trim();
    if (!namaBahasa) {
      errorMessage.textContent = 'Nama bahasa wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaBahasaInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaBahasa.length > 100) {
      errorMessage.textContent = 'Nama bahasa tidak boleh lebih dari 100 karakter.';
      errorMessage.classList.remove('hidden');
      namaBahasaInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data bahasa ini?' 
      : 'Apakah Anda yakin ingin menyimpan data bahasa ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection