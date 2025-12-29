@extends('layouts.app-admin')

@section('title', 'Edit Barang')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Barang</h2>
    
    @include('admin.data-barang._form', [
      'barang' => $barang,
      'kategoriBarang' => $kategoriBarang,
      'supplier' => $supplier,
      'satuan' => $satuan
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#barangForm');
  if (!form) return;

  const namaInput       = document.querySelector('#nama_barang');
  const kategoriSelect  = document.querySelector('#id_kategori_barang');
  const supplierSelect  = document.querySelector('#id_supplier');
  const satuanSelect    = document.querySelector('#id_satuan');
  const merkInput       = document.querySelector('#merk_barang');
  const beratInput      = document.querySelector('#berat');
  const marginInput     = document.querySelector('#margin');
  const kenaPpnRadios   = document.querySelectorAll('input[name="kena_ppn"]');
  const submitButton    = document.querySelector('#submitButton');

  const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';

  // Normalisasi nilai margin untuk perbandingan
  function normalizeMargin(value) {
    const num = parseFloat(value || 0);
    return isNaN(num) ? 0 : num;
  }

  // Format tampilan margin 
  function formatMarginDisplay() {
    if (!marginInput) return;
    const val = parseFloat(marginInput.value);
    if (isNaN(val) || val === 0) {
      marginInput.value = '0';
    } else if (Number.isInteger(val)) {
      marginInput.value = val.toFixed(0); // Hilangkan .00 pada bilangan bulat
    }
  }

  const initial = {
    nama:       namaInput?.value.trim() || '',
    kategori:   kategoriSelect?.value || '',
    supplier:   supplierSelect?.value || '',
    satuan:     satuanSelect?.value || '',
    merk:       merkInput?.value.trim() || '',
    berat:      beratInput?.value.trim() || '',
    margin:     normalizeMargin(marginInput?.value),
    kena_ppn:   document.querySelector('input[name="kena_ppn"]:checked')?.value || ''
  };

  // FITUR 1: Disable tombol Update kalau tidak ada perubahan 
  if (isEdit) {
    submitButton.disabled = true;
    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
  }

  function checkChanges() {
    const current = {
      nama:       namaInput?.value.trim() || '',
      kategori:   kategoriSelect?.value || '',
      supplier:   supplierSelect?.value || '',
      satuan:     satuanSelect?.value || '',
      merk:       merkInput?.value.trim() || '',
      berat:      beratInput?.value.trim() || '',
      margin:     normalizeMargin(marginInput?.value),
      kena_ppn:   document.querySelector('input[name="kena_ppn"]:checked')?.value || ''
    };

    const hasChanged = Object.keys(initial).some(key => initial[key] !== current[key]);

    if (hasChanged) {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
  }

  // FITUR 2: Format margin saat load dan saat interaksi 
  formatMarginDisplay(); // Jalankan sekali saat halaman load

  if (marginInput) {
    marginInput.addEventListener('input', formatMarginDisplay);
    marginInput.addEventListener('blur', formatMarginDisplay);
  }

  // Listener deteksi perubahan 
  [namaInput, kategoriSelect, supplierSelect, satuanSelect, merkInput, beratInput, marginInput].forEach(el => {
    if (el) {
      el.addEventListener('input', checkChanges);
      el.addEventListener('change', checkChanges);
      el.addEventListener('blur', checkChanges);
    }
  });

  kenaPpnRadios.forEach(radio => {
    radio.addEventListener('change', checkChanges);
  });
});
</script>
@endsection