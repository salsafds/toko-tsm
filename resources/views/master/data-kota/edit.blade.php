@extends('layouts.appmaster')

@section('title', 'Edit Kota')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Kota</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-kota._form', [
      'action' => route('master.data-kota.update', $kota->id_kota),
      'method' => 'PUT',
      'kota' => $kota,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#kotaForm');
  const namaKotaInput = document.querySelector('#nama_kota');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_kota_error');

  if (!form || !namaKotaInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_kota
  const initialNamakota = namaKotaInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaKota = namaKotaInput.value.trim();
    if (currentNamaKota === initialNamaKota) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_kota
  namaKotaInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaKotaInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaKota = namaKotaInput.value.trim();
    if (!namaKota) {
      errorMessage.textContent = 'Nama kota wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaKotaInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaKota.length > 50) {
      errorMessage.textContent = 'Nama kota tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      namaKotaInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data kota ini?' 
      : 'Apakah Anda yakin ingin menyimpan data kota ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection