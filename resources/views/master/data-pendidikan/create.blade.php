@extends('layouts.appmaster')

@section('title', 'Tambah Pendidikan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Pendidikan</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-pendidikan._form', [
      'action' => route('master.data-pendidikan.store'),
      'method' => 'POST',
      'pendidikan' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#pendidikanForm');
  const tingkatPendidikanInput = document.querySelector('#tingkat_pendidikan');
  const errorMessage = document.querySelector('#tingkat_pendidikan_error');

  if (!form || !tingkatPendidikanInput || !errorMessage) return;

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
    if (tingkatPendidikan.length > 100) {
      errorMessage.textContent = 'Tingkat pendidikan tidak boleh lebih dari 100 karakter.';
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