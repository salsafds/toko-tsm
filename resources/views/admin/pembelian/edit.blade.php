@extends('layouts.app-admin')

@section('title', 'Edit Pembelian')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Pembelian</h2>

    @include('admin.pembelian._form', [
      'action' => route('admin.pembelian.update', $pembelian->id_pembelian),
      'method' => 'PUT',
      'pembelian' => $pembelian,
      'suppliers' => $suppliers,
      'users' => $users,
      'barangs' => $barangs,
      'isEdit' => true
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#pembelianForm');
  const tanggalInput = document.querySelector('#tanggal_pembelian');
  const supplierSelect = document.querySelector('#id_supplier');
  const userSelect = document.querySelector('#id_user');
  const jenisSelect = document.querySelector('#jenis_pembayaran');
  const jumlahInput = document.querySelector('#jumlah_bayar');
  const submitButton = document.querySelector('#submitButton');

  const tanggalError = document.querySelector('#tanggal_pembelian_error');
  const supplierError = document.querySelector('#id_supplier_error');
  const userError = document.querySelector('#id_user_error');
  const jenisError = document.querySelector('#jenis_pembayaran_error');
  const jumlahError = document.querySelector('#jumlah_bayar_error');

  if (!form) return;

  // Simpan nilai awal
  const initial = {
    tanggal: tanggalInput?.value || '',
    supplier: supplierSelect?.value || '',
    user: userSelect?.value || '',
    jenis: jenisSelect?.value || '',
    jumlah: jumlahInput?.value || ''
  };

  function checkChanges() {
    const current = {
      tanggal: tanggalInput?.value || '',
      supplier: supplierSelect?.value || '',
      user: userSelect?.value || '',
      jenis: jenisSelect?.value || '',
      jumlah: jumlahInput?.value || ''
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

  [tanggalInput, supplierSelect, userSelect, jenisSelect, jumlahInput].forEach(el => {
    if (!el) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset error
    [tanggalError, supplierError, userError, jenisError, jumlahError].forEach(el => {
      if (el) el.textContent = '';
    });
    [tanggalInput, supplierSelect, userSelect, jenisSelect, jumlahInput].forEach(el => {
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    if (!tanggalInput.value) {
      tanggalError.textContent = 'Tanggal pembelian wajib diisi.';
      tanggalInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!supplierSelect.value) {
      supplierError.textContent = 'Supplier wajib dipilih.';
      supplierSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!userSelect.value) {
      userError.textContent = 'User wajib dipilih.';
      userSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!jenisSelect.value) {
      jenisError.textContent = 'Jenis pembayaran wajib dipilih.';
      jenisSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!jumlahInput.value || parseFloat(jumlahInput.value) <= 0) {
      jumlahError.textContent = 'Jumlah bayar harus lebih dari 0.';
      jumlahInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    if (confirm('Apakah Anda yakin ingin menyimpan perubahan ini?')) {
      form.submit();
    }
  });
});
</script>
@endsection
