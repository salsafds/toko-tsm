@extends('layouts.appmaster')

@section('title', 'Edit Bahasa')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Bahasa</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-bahasa._form', [
      'action' => route('master.data-bahasa.update', $bahasa->id_bahasa),
      'method' => 'PUT',
      'bahasa' => $bahasa,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#bahasaForm');
  const namaBahasaInput = document.querySelector('#nama_bahasa');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_bahasa_error');

  if (!form || !namaBahasaInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_bahasa
  const initialNamaBahasa = namaBahasaInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaBahasa = namaBahasaInput.value.trim();
    if (currentNamaBahasa === initialNamaBahasa) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_bahasa
  namaBahasaInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
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
    if (namaBahasa.length > 50) {
      errorMessage.textContent = 'Nama bahasa tidak boleh lebih dari 50 karakter.';
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