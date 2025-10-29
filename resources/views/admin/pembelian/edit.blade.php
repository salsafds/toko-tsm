Edit.blade.php
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
  const submitButton = document.querySelector('#submitButton');

  // Simpan nilai awal untuk deteksi perubahan
  const initial = {
    tanggal_pembelian: document.querySelector('[name="tanggal_pembelian"]').value,
    id_supplier: document.querySelector('[name="id_supplier"]').value,
    id_user: document.querySelector('[name="id_user"]').value,
    jenis_pembayaran: document.querySelector('[name="jenis_pembayaran"]').value,
    jumlah_bayar: document.querySelector('[name="jumlah_bayar"]').value,
  };

  function checkChanges() {
    const current = {
      tanggal_pembelian: document.querySelector('[name="tanggal_pembelian"]').value,
      id_supplier: document.querySelector('[name="id_supplier"]').value,
      id_user: document.querySelector('[name="id_user"]').value,
      jenis_pembayaran: document.querySelector('[name="jenis_pembayaran"]').value,
      jumlah_bayar: document.querySelector('[name="jumlah_bayar"]').value,
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

  document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });
});
</script>
@endsection