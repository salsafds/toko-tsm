@extends('layouts.app-admin')

@section('title', 'Tambah Barang')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Barang</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('admin.data-barang._form', [
      'action' => route('admin.data-barang.store'),
      'method' => 'POST',
      'barang' => null,
      'nextId' => $nextId,
      'kategoriBarang' => $kategoriBarang,
      'supplier' => $supplier,
      'satuan' => $satuan,
      'isEdit' => false
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#barangForm');
  const namaInput = document.querySelector('#nama_barang');
  const kategoriSelect = document.querySelector('#id_kategori_barang');
  const supplierSelect = document.querySelector('#id_supplier');
  const satuanSelect = document.querySelector('#id_satuan');
  const merkInput = document.querySelector('#merk_barang');
  const beratInput = document.querySelector('#berat');

  const namaError = document.querySelector('#nama_barang_error');
  const kategoriError = document.querySelector('#id_kategori_barang_error');
  const supplierError = document.querySelector('#id_supplier_error');
  const satuanError = document.querySelector('#id_satuan_error');
  const merkError = document.querySelector('#merk_barang_error');
  const beratError = document.querySelector('#berat_error');

  if (!form || !namaInput || !kategoriSelect || !supplierSelect || !satuanSelect || !merkInput || !beratInput) {
    console.error('Salah satu elemen input tidak ditemukan!');
    return;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset errors
    [namaError, kategoriError, supplierError, satuanError, merkError, beratError].forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });
    [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput].forEach(el => {
      el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    const nama = namaInput.value.trim();
    const kategori = kategoriSelect.value;
    const supplier = supplierSelect.value;
    const satuan = satuanSelect.value;
    const merk = merkInput.value.trim();
    const berat = beratInput.value.trim();

    if (!nama) {
      namaError.textContent = 'Nama barang wajib diisi.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (nama.length > 100) {
      namaError.textContent = 'Nama barang tidak boleh lebih dari 100 karakter.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!kategori) {
      kategoriError.textContent = 'Kategori barang wajib dipilih.';
      kategoriError.classList.remove('hidden');
      kategoriSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!supplier) {
      supplierError.textContent = 'Supplier wajib dipilih.';
      supplierError.classList.remove('hidden');
      supplierSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!satuan) {
      satuanError.textContent = 'Satuan wajib dipilih.';
      satuanError.classList.remove('hidden');
      satuanSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (merk && merk.length > 100) {
      merkError.textContent = 'Merk barang tidak boleh lebih dari 100 karakter.';
      merkError.classList.remove('hidden');
      merkInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!berat) {
      beratError.textContent = 'Berat wajib diisi.';
      beratError.classList.remove('hidden');
      beratInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (isNaN(parseFloat(berat)) || parseFloat(berat) <= 0) {
      beratError.textContent = 'Berat harus lebih dari 0.';
      beratError.classList.remove('hidden');
      beratInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) {
      console.log('Validation failed:', { nama, kategori, supplier, satuan, merk, berat });
      return;
    }

    if (confirm('Apakah Anda yakin ingin menyimpan data barang ini?')) {
      console.log('Submitting form');
      form.submit();
    }
  });
});
@endsection