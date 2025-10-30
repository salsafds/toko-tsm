@extends('layouts.app-admin')

@section('title', 'Tambah Pembelian')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Pembelian</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('admin.pembelian._form', [
      'action' => route('admin.pembelian.store'),
      'method' => 'POST',
      'pembelian' => null,
      'nextId' => $nextId,
      'suppliers' => $suppliers,
      'users' => $users,
      'barangs' => $barangs,
      'isEdit' => false
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

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset error messages
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

    if (!jumlahInput.value || isNaN(parseFloat(jumlahInput.value)) || parseFloat(jumlahInput.value) <= 0) {
      jumlahError.textContent = 'Jumlah bayar harus diisi dan lebih dari 0.';
      jumlahInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (hasError) return;

    if (confirm('Apakah Anda yakin ingin menyimpan data pembelian ini?')) {
      form.submit();
    }
  });
});
</script>
@endsection
