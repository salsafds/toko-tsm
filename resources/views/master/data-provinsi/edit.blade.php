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
      'isEdit' => true,
      'negara' => $negara
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#provinsiForm');
  const namaProvinsiInput = document.querySelector('#nama_provinsi');
  const negaraSelect = document.querySelector('#id_negara');
  const submitButton = document.querySelector('#submitButton');

  const namaError = document.querySelector('#nama_provinsi_error');
  const negaraError = document.querySelector('#id_negara_error');

  if (!form || !namaProvinsiInput || !namaError || !negaraSelect || !negaraError || !submitButton) return;

  // Simpan nilai awal untuk mendeteksi perubahan
  const initial = {
    nama: namaProvinsiInput.value.trim(),
    negara: negaraSelect.value || ''
  };

  function checkChanges() {
    const current = {
      nama: namaProvinsiInput.value.trim(),
      negara: negaraSelect.value || ''
    };
    const same = initial.nama === current.nama && initial.negara === current.negara;
    if (same) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pasang listener perubahan
  namaProvinsiInput.addEventListener('input', checkChanges);
  negaraSelect.addEventListener('change', checkChanges);

  // Validasi + konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset pesan error dan styling
    namaError.textContent = '';
    namaError.classList.add('hidden');
    namaProvinsiInput.classList.remove('border-red-500', 'bg-red-50');

    negaraError.textContent = '';
    negaraError.classList.add('hidden');
    negaraSelect.classList.remove('border-red-500', 'bg-red-50');

    let hasError = false;
    const namaProvinsi = namaProvinsiInput.value.trim();
    const idNegara = negaraSelect.value;

    if (!namaProvinsi) {
      namaError.textContent = 'Nama provinsi wajib diisi.';
      namaError.classList.remove('hidden');
      namaProvinsiInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (namaProvinsi.length > 100) {
      namaError.textContent = 'Nama provinsi tidak boleh lebih dari 100 karakter.';
      namaError.classList.remove('hidden');
      namaProvinsiInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!idNegara) {
      negaraError.textContent = 'Negara wajib dipilih.';
      negaraError.classList.remove('hidden');
      negaraSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data provinsi ini?' 
      : 'Apakah Anda yakin ingin menyimpan data provinsi ini?';

    if (confirm(message)) {
      form.submit();
    }
  });

  // initial check supaya tombol disable saat belum berubah
  checkChanges();
});
</script>

@endsection