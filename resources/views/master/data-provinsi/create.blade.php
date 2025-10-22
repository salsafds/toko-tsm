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
      'isEdit' => false,
      'negara' => $negara
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#provinsiForm');
  const namaProvinsiInput = document.querySelector('#nama_provinsi');
  const negaraSelect = document.querySelector('#id_negara');

  const namaError = document.querySelector('#nama_provinsi_error');
  const negaraError = document.querySelector('#id_negara_error');

  if (!form || !namaProvinsiInput || !namaError || !negaraSelect || !negaraError) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // cegah submit default

    // Reset pesan error dan styling
    namaError.textContent = '';
    namaError.classList.add('hidden');
    namaProvinsiInput.classList.remove('border-red-500', 'bg-red-50');

    negaraError.textContent = '';
    negaraError.classList.add('hidden');
    negaraSelect.classList.remove('border-red-500', 'bg-red-50');

    // Ambil nilai
    const namaProvinsi = namaProvinsiInput.value.trim();
    const idNegara = negaraSelect.value;

    let hasError = false;

    // Validasi nama provinsi
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

    // Validasi negara
    if (!idNegara) {
      negaraError.textContent = 'Negara wajib dipilih.';
      negaraError.classList.remove('hidden');
      negaraSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    // Konfirmasi sebelum submit
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data provinsi ini?' 
      : 'Apakah Anda yakin ingin menyimpan data provinsi ini?';

    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>

@endsection