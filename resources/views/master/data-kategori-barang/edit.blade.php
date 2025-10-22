@extends('layouts.appmaster')

@section('title', 'Edit Kategori Barang')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Kategori Barang</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-kategori-barang._form', [
      'action' => route('master.data-kategori-barang.update', $kategori->id_kategori_barang),
      'method' => 'PUT',
      'kategori' => $kategori,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#kategoriForm');
  const namaKategoriInput = document.querySelector('#nama_kategori');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_kategori_error');

  if (!form || !namaKategoriInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_kategori
  const initialNamaKategori = namaKategoriInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaKategori = namaKategoriInput.value.trim();
    if (currentNamaKategori === initialNamaKategori) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_kategori
  namaKategoriInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
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