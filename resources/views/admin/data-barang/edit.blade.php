@extends('layouts.app-admin')

@section('title', 'Edit Barang')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Barang</h2>
    @include('admin.data-barang._form', [
      'action' => route('admin.data-barang.update', $barang->id_barang),
      'method' => 'PUT',
      'barang' => $barang,
      'kategoriBarang' => $kategoriBarang,
      'supplier' => $supplier,
      'satuan' => $satuan,
      'isEdit' => true
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
  const submitButton = document.querySelector('#submitButton');

  const namaError = document.querySelector('#nama_barang_error');
  const kategoriError = document.querySelector('#id_kategori_barang_error');
  const supplierError = document.querySelector('#id_supplier_error');
  const satuanError = document.querySelector('#id_satuan_error');
  const merkError = document.querySelector('#merk_barang_error');
  const beratError = document.querySelector('#berat_error');

  if (!form) return;

  // Simpan nilai awal (hanya atribut dasar)
  const initial = {
    nama: namaInput?.value.trim() || '',
    kategori: kategoriSelect?.value || '',
    supplier: supplierSelect?.value || '',
    satuan: satuanSelect?.value || '',
    merk: merkInput?.value.trim() || '',
    berat: beratInput?.value.trim() || ''
  };

  function checkChanges() {
    const current = {
      nama: namaInput?.value.trim() || '',
      kategori: kategoriSelect?.value || '',
      supplier: supplierSelect?.value || '',
      satuan: satuanSelect?.value || '',
      merk: merkInput?.value.trim() || '',
      berat: beratInput?.value.trim() || ''
    };

    const same = Object.keys(initial).every(k => initial[k] === current[k]);
    if (same) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput].forEach(el => {
    if (!el) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset
    [namaError, kategoriError, supplierError, satuanError, merkError, beratError].forEach(el => {
      if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput].forEach(el => {
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    const nama = namaInput?.value.trim() || '';
    const kategori = kategoriSelect?.value || '';
    const supplier = supplierSelect?.value || '';
    const satuan = satuanSelect?.value || '';
    const merk = merkInput?.value.trim() || '';
    const berat = beratInput?.value.trim() || '';

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
    } else if (parseFloat(berat) <= 0) {
      beratError.textContent = 'Berat harus lebih dari 0.';
      beratError.classList.remove('hidden');
      beratInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data barang ini?' : 'Apakah Anda yakin ingin menyimpan data barang ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
@endsection