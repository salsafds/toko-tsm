@extends('layouts.appmaster')

@section('title', 'Tambah Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Satuan</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-satuan._form', [
      'action' => route('master.data-satuan.store'),
      'method' => 'POST',
      'satuan' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#satuanForm');
  const namaSatuanInput = document.querySelector('#nama_satuan');
  const errorMessage = document.querySelector('#nama_satuan_error');

  if (!form || !namaSatuanInput || !errorMessage) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Validasi client-side
    const namaSatuan = namaSatuanInput.value.trim();
    if (!namaSatuan) {
      errorMessage.textContent = 'Nama satuan wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaSatuanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    } else {
      errorMessage.textContent = '';
      errorMessage.classList.add('hidden');
      namaSatuanInput.classList.remove('border-red-500', 'bg-red-50');
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data satuan ini?' 
      : 'Apakah Anda yakin ingin menyimpan data satuan ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection