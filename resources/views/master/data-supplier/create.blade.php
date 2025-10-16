@extends('layouts.appmaster')

@section('title', 'Tambah Supplier')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Supplier</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-supplier._form', [
      'action' => route('master.data-supplier.store'),
      'method' => 'POST',
      'supplier' => null,
      'nextId' => $nextId,
      'isEdit' => false
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#supplierForm');
  const namaInput = document.querySelector('#nama_supplier');
  const alamatInput = document.querySelector('#alamat');
  const namaError = document.querySelector('#nama_supplier_error');
  const alamatError = document.querySelector('#alamat_error');

  if (!form || !namaInput || !alamatInput || !namaError || !alamatError) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // reset errors
    namaError.textContent = '';
    namaError.classList.add('hidden');
    namaInput.classList.remove('border-red-500', 'bg-red-50');

    alamatError.textContent = '';
    alamatError.classList.add('hidden');
    alamatInput.classList.remove('border-red-500', 'bg-red-50');

    // client-side validation
    const nama = namaInput.value.trim();
    const alamat = alamatInput.value.trim();
    let hasError = false;

    if (!nama) {
      namaError.textContent = 'Nama supplier wajib diisi.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (nama.length > 191) {
      namaError.textContent = 'Nama supplier terlalu panjang.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!alamat) {
      alamatError.textContent = 'Alamat wajib diisi.';
      alamatError.classList.remove('hidden');
      alamatInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data supplier ini?' : 'Apakah Anda yakin ingin menyimpan data supplier ini?';

    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>
@endsection
