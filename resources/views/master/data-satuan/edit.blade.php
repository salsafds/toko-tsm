@extends('layouts.appmaster')

@section('title', 'Edit Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Satuan</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-satuan._form', [
      'action' => route('master.data-satuan.update', $satuan->id_satuan),
      'method' => 'PUT',
      'satuan' => $satuan,
      'isEdit' => true
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#satuanForm');
  const namaSatuanInput = document.querySelector('#nama_satuan');
  const submitButton = document.querySelector('#submitButton');
  const errorMessage = document.querySelector('#nama_satuan_error');

  if (!form || !namaSatuanInput || !submitButton || !errorMessage) return;

  // Simpan nilai awal nama_satuan
  const initialNamaSatuan = namaSatuanInput.value.trim();

  // Fungsi untuk memeriksa perubahan
  function checkChanges() {
    const currentNamaSatuan = namaSatuanInput.value.trim();
    if (currentNamaSatuan === initialNamaSatuan) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  // Pantau perubahan pada input nama_satuan
  namaSatuanInput.addEventListener('input', checkChanges);

  // Validasi dan konfirmasi saat submit
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Cegah submit default

    // Reset pesan error dan styling
    errorMessage.textContent = '';
    errorMessage.classList.add('hidden');
    namaSatuanInput.classList.remove('border-red-500', 'bg-red-50');

    // Validasi client-side
    const namaSatuan = namaSatuanInput.value.trim();
    if (!namaSatuan) {
      errorMessage.textContent = 'Nama satuan wajib diisi.';
      errorMessage.classList.remove('hidden');
      namaSatuanInput.classList.add('border-red-500', 'bg-red-50');
      return;
    }
    if (namaSatuan.length > 50) {
      errorMessage.textContent = 'Nama satuan tidak boleh lebih dari 50 karakter.';
      errorMessage.classList.remove('hidden');
      namaSatuanInput.classList.add('border-red-500', 'bg-red-50');
      return;
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