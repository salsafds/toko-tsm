@extends('layouts.appmaster')

@section('title', 'Tambah Kota')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Kota</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-kota._form', [
      'action' => route('master.data-kota.store'),
      'method' => 'POST',
      'kota' => null,
      'nextId' => $nextId,
      'isEdit' => false,
      'negara' => $negara,
      'provinsi' => $provinsi
    ])

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#kotaForm');
  const namaKotaInput = document.querySelector('#nama_kota');
  const negaraSelect = document.querySelector('#id_negara');
  const provinsiSelect = document.querySelector('#id_provinsi');

  const namaError = document.querySelector('#nama_kota_error');
  const negaraError = document.querySelector('#id_negara_error');
  const provinsiError = document.querySelector('#id_provinsi_error');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset
    [namaError, negaraError, provinsiError].forEach(el => { if (el) { el.textContent = ''; el.classList.add('hidden'); }});
    [namaKotaInput, negaraSelect, provinsiSelect].forEach(el => { if (el) el.classList.remove('border-red-500', 'bg-red-50'); });

    let hasError = false;
    const nama = namaKotaInput?.value.trim() || '';
    const negara = negaraSelect?.value || '';
    const provinsi = provinsiSelect?.value || '';

    if (!nama) {
      namaError.textContent = 'Nama kota wajib diisi.';
      namaError.classList.remove('hidden');
      namaKotaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (nama.length > 100) {
      namaError.textContent = 'Nama kota tidak boleh lebih dari 100 karakter.';
      namaError.classList.remove('hidden');
      namaKotaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!negara) {
      negaraError.textContent = 'Negara wajib dipilih.';
      negaraError.classList.remove('hidden');
      negaraSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!provinsi) {
      provinsiError.textContent = 'Provinsi wajib dipilih.';
      provinsiError.classList.remove('hidden');
      provinsiSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data kota ini?' 
      : 'Apakah Anda yakin ingin menyimpan data kota ini?';

    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>

@endsection