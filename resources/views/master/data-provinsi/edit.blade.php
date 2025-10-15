@extends('layouts.appmaster')

@section('title', 'Edit Provinsi')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Provinsi</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-provinsi._form', [
      'action' => route('master.data-provinsi.update', $provinsi->id_provinsi),
      'method' => 'PUT',
      'provinsi' => $provinsi,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#provinsiForm');
  const namaProvinsiInput = document.querySelector('#nama_provinsi');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_provinsi_error');

  if (!form || !namaProvinsiInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_provinsi
  const initialNamaProvinsi = namaProvinsiInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaProvinsi = namaProvinsiInput.value.trim();
    if (currentNamaProvinsi === initialNamaProvinsi) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_provinsi
  namaProvinsiInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
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
    if (namaProvinsi.length > 50) {
      errorMessage.textContent = 'Nama provinsi tidak boleh lebih dari 50 karakter.';
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