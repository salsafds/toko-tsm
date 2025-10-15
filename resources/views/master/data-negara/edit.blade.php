@extends('layouts.appmaster')

@section('title', 'Edit Negara')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Negara</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-negara._form', [
      'action' => route('master.data-negara.update', $negara->id_negara),
      'method' => 'PUT',
      'negara' => $negara,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#negaraForm');
  const namaNegaraInput = document.querySelector('#nama_negara');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_negara_error');

  if (!form || !namaNegaraInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_negara
  const initialNamaNegara = namaNegaraInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaNegara = namaNegaraInput.value.trim();
    if (currentNamaNegara === initialNamaNegara) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_negara
  namaNegaraInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaNegaraInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaNegara = namaNegaraInput.value.trim();
    if (!namaNegara) {
      errorMessage.textContent = 'Nama negara wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaNegaraInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaNegara.length > 50) {
      errorMessage.textContent = 'Nama negara tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      namaNegaraInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data negara ini?' 
      : 'Apakah Anda yakin ingin menyimpan data negara ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection