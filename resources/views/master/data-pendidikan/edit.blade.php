@extends('layouts.appmaster')

@section('title', 'Edit Pendidikan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Pendidikan</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-pendidikan._form', [
      'action' => route('master.data-pendidikan.update', $pendidikan->id_pendidikan),
      'method' => 'PUT',
      'pendidikan' => $pendidikan,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#pendidikanForm');
  const tingkatPendidikanInput = document.querySelector('#tingkat_pendidikan');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#tingkat_pendidikan_error');

  if (!form || !tingkatPendidikanInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal tingkat_pendidikan
  const initialTingkatPendidikan = tingkatPendidikanInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentTingkatPendidikan = tingkatPendidikanInput.value.trim();
    if (currentTingkatPendidikan === initialTingkatPendidikan) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input tingkat_pendidikan
  tingkatPendidikanInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    tingkatPendidikanInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const tingkatPendidikan = tingkatPendidikanInput.value.trim();
    if (!tingkatPendidikan) {
      errorMessage.textContent = 'Tingkat pendidikan wajib diisi.';
      errorMessage.classList.remove('hidden');
      tingkatPendidikanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (tingkatPendidikan.length > 50) {
      errorMessage.textContent = 'Tingkat pendidikan tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      tingkatPendidikanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data pendidikan ini?' 
      : 'Apakah Anda yakin ingin menyimpan data pendidikan ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection