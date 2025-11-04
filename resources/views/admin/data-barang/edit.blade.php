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
  const marginInput = document.querySelector('#margin');
  const submitButton = document.querySelector('#submitButton');

  const namaError = document.querySelector('#nama_barang_error');
  const kategoriError = document.querySelector('#id_kategori_barang_error');
  const supplierError = document.querySelector('#id_supplier_error');
  const satuanError = document.querySelector('#id_satuan_error');
  const merkError = document.querySelector('#merk_barang_error');
  const beratError = document.querySelector('#berat_error');
  const marginError = document.querySelector('#margin_error');

  if (!form) return;

  // Deteksi apakah mode edit (berdasarkan input _method PUT)
  const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';

  // Fungsi untuk normalisasi nilai margin (ke number, default 0)
  function normalizeMargin(value) {
    const num = parseFloat(value || 0);
    return isNaN(num) ? 0 : num;
  }

  // Simpan nilai awal (dengan normalisasi untuk margin)
  const initial = {
    nama: namaInput?.value.trim() || '',
    kategori: kategoriSelect?.value || '',
    supplier: supplierSelect?.value || '',
    satuan: satuanSelect?.value || '',
    merk: merkInput?.value.trim() || '',
    berat: beratInput?.value.trim() || '',
    margin: normalizeMargin(marginInput?.value) // Normalisasi ke number
  };

  // Set tombol disabled default untuk mode edit
  if (isEdit) {
    submitButton.disabled = true;
    submitButton.classList.add('opacity-50');
  }

  function checkChanges() {
    const current = {
      nama: namaInput?.value.trim() || '',
      kategori: kategoriSelect?.value || '',
      supplier: supplierSelect?.value || '',
      satuan: satuanSelect?.value || '',
      merk: merkInput?.value.trim() || '',
      berat: beratInput?.value.trim() || '',
      margin: normalizeMargin(marginInput?.value) // Normalisasi ke number
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

  // Event listener untuk mendeteksi perubahan (tambahkan 'blur' untuk input number)
  [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput, marginInput].forEach(el => {
    if (!el) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
    if (el.type === 'number') {
      el.addEventListener('blur', checkChanges); // Pastikan perubahan terdeteksi saat blur
    }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset error
    [namaError, kategoriError, supplierError, satuanError, merkError, beratError, marginError].forEach(el => {
      if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput, marginInput].forEach(el => {
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    const nama = namaInput?.value.trim() || '';
    const kategori = kategoriSelect?.value || '';
    const supplier = supplierSelect?.value || '';
    const satuan = satuanSelect?.value || '';
    const merk = merkInput?.value.trim() || '';
    const berat = beratInput?.value.trim() || '';
    const margin = marginInput?.value.trim() || '';

    // Validasi lainnya tetap sama...

    // Validasi margin
    if (margin && (parseFloat(margin) < 0 || parseFloat(margin) > 100)) {
      marginError.textContent = 'Margin harus antara 0 dan 100.';
      marginError.classList.remove('hidden');
      marginInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    // Konfirmasi
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data barang ini?' : 'Apakah Anda yakin ingin menyimpan data barang ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>
@endsection