@extends('layouts.appmaster')

@section('title', 'Tambah Kategori Barang')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Kategori Barang</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-kategori-barang._form', [
      'action' => route('master.data-kategori-barang.store'),
      'method' => 'POST',
      'kategori' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#kategoriForm');
  const namaKategoriInput = document.querySelector('#nama_kategori');
  const errorMessage = document.querySelector('#nama_kategori_error');

  if (!form || !namaKategoriInput || !errorMessage) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaKategoriInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaKategori = namaKategoriInput.value.trim();
    if (!namaKategori) {
      errorMessage.textContent = 'Nama kategori wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaKategoriInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaKategori.length > 50) {
      errorMessage.textContent = 'Nama kategori tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      namaKategoriInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin mengedit data kategori ini?' 
      : 'Apakah Anda yakin ingin menyimpan data kategori ini?';

    if (confirm(message)) {
      form.submit(); // Lanjutkan submit jika dikonfirmasi
    }
  });
});
</script>
@endsection