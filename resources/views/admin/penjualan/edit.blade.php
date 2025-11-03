@extends('layouts.app-admin')

@section('title', 'Edit Penjualan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Penjualan</h2>
    
    @include('admin.penjualan._form', [
      'action' => route('admin.penjualan.update', $penjualan->id_penjualan),
      'method' => 'PUT',
      'penjualan' => $penjualan,
      'pelanggans' => $pelanggans,
      'anggotas' => $anggotas,
      'barangs' => $barangs,
      'agenEkspedisis' => $agenEkspedisis,
      'isEdit' => true
    ])
  </div>
</div>
@endsection